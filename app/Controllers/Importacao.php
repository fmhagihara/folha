<?php

namespace App\Controllers;

use App\Models\ImportacaoModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SimpleXMLElement;

class Importacao extends BaseController
{
    public function index(): string
    {

        return view('importacao/inicio');
    }

    function processar()
    {
        $arquivo = $_FILES['arquivo']['tmp_name'];

        // Carrega o arquivo XLSX
        $spreadsheet = IOFactory::load($arquivo);

        // Seleciona a primeira planilha (worksheet)
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];

        // Itera pelas linhas da planilha
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];

            // Itera pelas células da linha
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Adiciona os dados da linha ao array associativo
            $data[] = $rowData;
        }

        $headers = array_shift($data);

        // Cria um array associativo para cada linha de dados
        $associativeData = [];
        foreach ($data as $row) {
            $associativeData[] = array_combine($headers, $row);
        }

        // Agora $associativeData contém os dados do arquivo XLSX em formato de array associativo
        echo '<pre>';
        print_r($associativeData);
    }


    function dat_analitico()
    {
        $arquivo = $_FILES['arquivo']['tmp_name'];
        $nomearquivo = $_FILES['arquivo']['name'];

        $competencia = $this->request->getPost('competencia');

        $model = new ImportacaoModel();
        $extensao = substr($nomearquivo, -3);
        if (strtolower($extensao) == 'dat') {
            $conteudo = iconv("ISO-8859-1", "UTF-8", file_get_contents($arquivo));
            $conteudo = str_replace('"', '', $conteudo);
            $nomearquivo = date('YmdHis') . '_importabenner.csv';
            file_put_contents($nomearquivo, $conteudo);
            sleep(2);
            $ponteiro = fopen($nomearquivo, 'r');
            $primeiralinha = fgets($ponteiro);

            $primeiralinha = str_replace(array('/', ' ', "'", "\r\n"), '', iconv("UTF-8", "ASCII//TRANSLIT", $primeiralinha));

            $nomesCampos = explode(';', strtolower($primeiralinha));

            $lista = array();
            while (!feof($ponteiro)) {

                $registro = array('competencia' => $competencia, 'arquivo' => $nomearquivo);
                $linha = fgets($ponteiro);
                if ($linha) {
                    $valoresCampos = explode(';', $linha);
                    foreach ($nomesCampos as $key => $nc) {
                        $registro[$nc] = $valoresCampos[$key];
                    }
                    $lista[] = $registro;
                }
            }
            fclose($ponteiro);
            $tam_lista = count($lista);
            if ($tam_lista) {
                $reg_inseridos = 0;
                foreach ($lista as $li) {
                    $inserido = $model->insert($li);

                    if ($inserido) $reg_inseridos++;
                }
            }
            echo "Lista: $tam_lista itens. Registros: $reg_inseridos inseridos.";
        } else {
            echo 'Formato n&atilde;o permitido';
        }
    }

    function listar_agrupado($mes = null)
    {
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agrupar($mes);
            $body_data['agrupado'] = $agrupado;
            $body_data['mes'] = $mes;
            return view('importacao/listar_agrupado', $body_data);
        }
    }


    function centro_custo($mes = null)
    {
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);
           // echo '<pre>';

            foreach ($agrupado as $ag) {
                if ($ag['tipo']) {
                    echo $ag['codigodaverba'] . ' - ' . $ag['tipo'] . ' - ' . $ag['centrodecusto'] . ' - ' . $ag['soma'] . ' - ' . $ag['nome_grupo'] . '<br>';
                }
            }

            //var_dump($agrupado);
            // $body_data['agrupado'] = $agrupado;
            // return view('importacao/listar_agrupado', $body_data);

        }
    }


    function gerar_xml($mes = null)
    {
        if ($mes) {

            // Dados do BD
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);


            // Variáveis para o Header
            $mesano = substr($mes, 5, 2) . substr($mes, 0, 4);
            $datacredito = date('tmY', strtotime($mes));
            $datahorageracao = date('dmYHi');

            // Header do XML
            $xml = '<Implanta>' . PHP_EOL .
                '  <Header NumeroLote="177" MesAno="' . $mesano . '" DataCreditoContabil="' . $datacredito .
                '" DataHoraGeracao="' . $datahorageracao . '" CNPJ="76639384000159" UF="PR" Versao="1.0" TipoArquivo="FolhaPagamento" SistemaOrigem="BENNER" SistemaDestino="SISCONT.NET" />' . PHP_EOL;

            // Variáveis para o miolo do XML
            $valorDesconto = array();
            $histDesconto = array();
            $contaDesconto = array();
            $qtdeBlocos = $qtdeRegistros = 0;

            $xml_BlocoA = $xml_BlocoC = $xml_BlocoF = '';

            $jafoi_BlocoA = array();

            foreach ($agrupado as $ag) {
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


    function agrupar_descontos()
    {
        $arquivo = $_FILES['arquivo']['tmp_name'];
        $nomearquivo = $_FILES['arquivo']['name'];

        $conteudo = file_get_contents($arquivo);

        $blocoC_inicio = strpos($conteudo, '<BlocoC>');
        $blocoC_fim = strpos($conteudo, '</BlocoC>');

        // Pega somente a parte que está dentro do bloco C
        $blocoC = substr($conteudo, $blocoC_inicio, $blocoC_fim - $blocoC_inicio + 9);

        // Convert xml string into an object
        $new = simplexml_load_string(trim($blocoC));

        // Convert into json
        $con = json_encode($new);

        // Convert into associative array
        $newArr = json_decode($con, true);

        $descontos = $newArr['Desconto'];

        $verba = $hverba = array();
        $anterior = '';
        foreach ($descontos as $desc) {
            $resumido = $desc['CodigoResumidoConta'];
            $historico = $desc['Historico'];
            $valor = $desc['Valor'];
            if ($resumido != $anterior) {
                $verba[$resumido] = $valor;
                $hverba[$resumido] = $historico;
            } else {
                $verba[$resumido] += $valor;
            }
            $anterior = $resumido;
        }

        $novoBlocoC = '<BlocoC>';
        foreach ($verba as $cod => $val) {
            $novoBlocoC .= '
    <Desconto>
      <Valor>' . $val . '</Valor>
      <Historico>' . $hverba[$cod] . '</Historico>
      <CodigoResumidoConta>' . $cod . '</CodigoResumidoConta>
    </Desconto>';
        }
        $novoBlocoC .= '
  </BlocoC>';

        $pos_qtd_registros = strpos($conteudo, 'QuantidadeTotalRegistros');
        $filtrar = substr($conteudo, $pos_qtd_registros + 26, 6);
        $qtde = preg_replace("/\D/", "", $filtrar);

        $novaQtde = (int) $qtde - count($descontos) + count($verba);

        $novoconteudo = substr($conteudo, 0, $blocoC_inicio) . $novoBlocoC . substr($conteudo, $blocoC_fim + 9);

        $antesRegistro = 'QuantidadeTotalRegistros="' . $qtde . '"';
        $depoisRegistro = 'QuantidadeTotalRegistros="' . $novaQtde . '"';

        echo 'Antes: ' . $antesRegistro . '<br>';
        echo 'Depois: ' . $depoisRegistro;
        str_ireplace($antesRegistro, $depoisRegistro, $novoconteudo);


        sleep(2);
        file_put_contents('modificado.xml', $novoconteudo);
    }

    function array_to_xml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this->array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}
