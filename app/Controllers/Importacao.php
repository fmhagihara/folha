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

            $primeiralinha = str_replace(array('/',' ', "'", "\r\n"), '', iconv("UTF-8", "ASCII//TRANSLIT", $primeiralinha));

            $nomesCampos = explode(';', strtolower($primeiralinha));

            $lista = array();
            while (!feof($ponteiro)) {

                $registro = array('competencia'=>'2023-08-01', 'arquivo'=>$nomearquivo);
                $linha = fgets($ponteiro);
                if ($linha) {
                    $valoresCampos = explode(';', $linha);
                    foreach ($nomesCampos as $key=>$nc) {
                        $registro[$nc] = $valoresCampos[$key];
                    }
                    $lista[] = $registro;
                }
            }
            fclose($ponteiro);
            $tam_lista = count($lista);
            if ($tam_lista) {
                $reg_inseridos = 0;
                foreach($lista as $li) {
                    $inserido = $model->insert($li);

                    if ($inserido) $reg_inseridos ++;
                }
            }
            echo "Lista: $tam_lista itens. Registros: $reg_inseridos inseridos.";

        }
        else {
            echo 'Formato n&atilde;o permitido';
        }
    }

    function listar_agrupado($mes=null) {
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agrupar($mes);
            //var_dump($agrupado);
            $body_data['agrupado'] = $agrupado;
            return view('importacao/listar_agrupado', $body_data);

        }
    }


    function agrupar_descontos() {
        $arquivo = $_FILES['arquivo']['tmp_name'];
        $nomearquivo = $_FILES['arquivo']['name'];

        $conteudo = file_get_contents($arquivo);

        $blocoC_inicio = strpos($conteudo, '<BlocoC>');
        $blocoC_fim = strpos($conteudo, '</BlocoC>');

        // Pega somente a parte que está dentro do bloco C
        $blocoC = substr($conteudo, $blocoC_inicio, $blocoC_fim-$blocoC_inicio+9);

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
            }
            else {
                $verba[$resumido] += $valor;
            }
            $anterior = $resumido;
        }

        $novoBlocoC = '<BlocoC>';
        foreach ($verba as $cod=>$val) {
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
        $filtrar = substr($conteudo, $pos_qtd_registros+26, 6);
        $qtde = preg_replace("/\D/","", $filtrar);

        $novaQtde = (int) $qtde - count($descontos) + count($verba);

        $novoconteudo = substr($conteudo, 0, $blocoC_inicio) . $novoBlocoC . substr($conteudo, $blocoC_fim+9);

        $antesRegistro = 'QuantidadeTotalRegistros="' . $qtde . '"';
        $depoisRegistro = 'QuantidadeTotalRegistros="' . $novaQtde . '"';

        echo 'Antes: ' . $antesRegistro . '<br>';
        echo 'Depois: ' . $depoisRegistro;
        str_replace($antesRegistro, $depoisRegistro, $novoconteudo);

        sleep(2);
        file_put_contents('modificado.xml', $novoconteudo);


    }

    function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this->array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
         }
    }
}