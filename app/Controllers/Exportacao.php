<?php

namespace App\Controllers;

use App\Models\ImportacaoModel;

class Exportacao extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }


    function gerar_xml($mes = null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        if ($mes) {

            // Dados do BD
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);


            // Variáveis para o Header
            $mesano = substr($mes, 5, 2) . substr($mes, 0, 4);
            $datacredito = date('tmY', strtotime($mes));

            // Quando é 13º salário, joga para dia 05 do mês
            if (substr($mes, 8, 2) == '15') $datacredito = '06'.date('mY', strtotime($mes));
            $datahorageracao = date('dmYHi');

            // Header do XML
            $xml = '<Implanta>' . PHP_EOL .
                '  <Header NumeroLote="177" MesAno="' . $mesano . '" DataCreditoContabil="' . $datacredito .
                '" DataHoraGeracao="' . $datahorageracao . '" CNPJ="76639384000159" UF="PR" Versao="1.0" TipoArquivo="FolhaPagamento" SistemaOrigem="BENNER_FolhaFMH" SistemaDestino="SISCONT.NET" />' . PHP_EOL;

            // Variáveis para o miolo do XML
            $valorDesconto = array();
            $histDesconto = array();
            $contaDesconto = array();
            $qtdeBlocos = $qtdeRegistros = 0;

            $xml_BlocoA = $xml_BlocoB = $xml_BlocoC = $xml_BlocoF = '';

            $jafoi_BlocoA = array();

            foreach ($agrupado as $ag) {
                if ($ag['exportar_xml']) {
                    $centrodecustof = substr($ag['centrodecusto'], 0, 1) . '.' . substr($ag['centrodecusto'], 1, 3) . '.' . substr($ag['centrodecusto'], 4, 2);

                    if ($ag['tipo'] === 'A - Despesa') {

                        // Como tem verbas "repetidas", ignora os repetidos
                        $verbaccusto = $ag['codigodaverba'] . '-' . $ag['centrodecusto'];
                        if (!in_array($verbaccusto, $jafoi_BlocoA)) {

                            $xml_BlocoA .= '    <Despesa>' . PHP_EOL;
                            $xml_BlocoA .= '      <Valor>' . number_format($ag['soma'], 2, '', '') . '</Valor>' . PHP_EOL;
                            $xml_BlocoA .= '      <Historico>' . $ag['nomedaverba'] . '</Historico>' . PHP_EOL;
                            $xml_BlocoA .= '      <CodigoConta>' . $ag['conta_despesa'] . '</CodigoConta>' . PHP_EOL;
                            $xml_BlocoA .= '      <CodigoCentroCusto>' . $centrodecustof . '</CodigoCentroCusto>' . PHP_EOL;
                            $xml_BlocoA .= '    </Despesa>' . PHP_EOL;

                            $qtdeRegistros++;

                            $jafoi_BlocoA[] = $verbaccusto;
                        }
                    }

                    if ($ag['tipo'] === 'B - Beneficio') {
                        $xml_BlocoB .= '    <Beneficio>' . PHP_EOL;
                        $xml_BlocoB .= '      <Valor>' . number_format($ag['soma'], 2, '', '') . '</Valor>' . PHP_EOL;
                        $xml_BlocoB .= '      <Historico>' . $ag['nomedaverba'] . '</Historico>' . PHP_EOL;
                        $xml_BlocoB .= '      <CodigoConta>' . $ag['conta_liquidacao'] . '</CodigoConta>' . PHP_EOL;
                        $xml_BlocoB .= '    </Beneficio>' . PHP_EOL;
                        $qtdeRegistros++;
                    }

                    if ($ag['tipo'] === 'C - Desconto') {
                        $cv = $ag['id_grupo'];
                        if (!isset($valorDesconto[$cv])) $valorDesconto[$cv] = $histDesconto[$cv] = $contaDesconto[$cv] = 0;
                        $valorDesconto[$cv] += $ag['soma'];
                        $histDesconto[$cv] = $ag['nome_grupo'];
                        $contaDesconto[$cv] = $ag['conta_banco'];
                    }

                    if ($ag['tipo'] === 'F - Estorno') {
                        $xml_BlocoF .= '    <Estorno>' . PHP_EOL;
                        $xml_BlocoF .= '      <Valor>' . number_format($ag['soma'], 2, '', '') . '</Valor>' . PHP_EOL;
                        $xml_BlocoF .= '      <Historico>' . $ag['nomedaverba'] . '</Historico>' . PHP_EOL;
                        $xml_BlocoF .= '      <DataEstorno>' . $datacredito . '</DataEstorno>' . PHP_EOL;
                        $xml_BlocoF .= '      <CodigoContaDespesa>' . $ag['conta_despesa'] . '</CodigoContaDespesa>' . PHP_EOL;
                        $xml_BlocoF .= '      <CodigoContaFinanceira>' . $ag['conta_banco'] . '</CodigoContaFinanceira>' . PHP_EOL;
                        $xml_BlocoF .= '      <CodigoCentroCusto>' . $centrodecustof . '</CodigoCentroCusto>' . PHP_EOL;
                        $xml_BlocoF .= '    </Estorno>' . PHP_EOL;
                        $qtdeRegistros++;
                    }
                }
            }

            // Grupos do bloco C
            if (!empty($valorDesconto)) {

                foreach ($valorDesconto as $key => $value) {
                    $xml_BlocoC .= '    <Desconto>' . PHP_EOL;
                    $xml_BlocoC .= '      <Valor>' . number_format($value, 2, '', '') . '</Valor>' . PHP_EOL;
                    $xml_BlocoC .= '      <Historico>' . $histDesconto[$key] . '</Historico>' . PHP_EOL;
                    $xml_BlocoC .= '      <CodigoConta>' . $contaDesconto[$key] . '</CodigoConta>' . PHP_EOL;
                    $xml_BlocoC .= '    </Desconto>' . PHP_EOL;
                    $qtdeRegistros++;
                }
                $valorDesconto = [];
            }

            // Para cada bloco, se tiver registros, inclui no XML principal
            if ($xml_BlocoA) {
                $qtdeBlocos++;
                $xml .= '  <BlocoA>' . PHP_EOL . $xml_BlocoA . '  </BlocoA>' . PHP_EOL;
            }

            if ($xml_BlocoB) {
                $qtdeBlocos++;
                $xml .= '  <BlocoB>' . PHP_EOL . $xml_BlocoB . '  </BlocoB>' . PHP_EOL;
            }

            if ($xml_BlocoC) {
                $qtdeBlocos++;
                $xml .= '  <BlocoC>' . PHP_EOL . $xml_BlocoC . '  </BlocoC>' . PHP_EOL;
            }

            if ($xml_BlocoF) {
                $qtdeBlocos++;
                $xml .= '  <BlocoF>' . PHP_EOL . $xml_BlocoF . '  </BlocoF>' . PHP_EOL;
            }

            // Trailler do XML
            $xml .= '  <Trailer QuantidadeBlocos="' . $qtdeBlocos . '" QuantidadeTotalRegistros="' . $qtdeRegistros . '" />' . PHP_EOL .
                '</Implanta>';


            // Nome do arquivo e caminho onde o arquivo XML será salvo temporariamente
            $nome_arquivo = "implanta.net.folha.$mesano.pr.xml";
            $caminho_arquivo = sys_get_temp_dir() . '/' . $nome_arquivo;

            // Criação do arquivo XML
            $file = fopen($caminho_arquivo, 'w');
            fwrite($file, $xml);
            fclose($file);


            // Define os cabeçalhos para forçar o download do arquivo
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $nome_arquivo);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($caminho_arquivo));

            // Envia o arquivo para o cliente
            readfile($caminho_arquivo);

            // Remove o arquivo temporário após o download
            unlink($caminho_arquivo);
        }
    }
}