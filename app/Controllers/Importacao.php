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
            echo "Lista: $tam_lista itens. Registros: $reg_inseridos inseridos.<br>";
            echo anchor('lista/agrupado/' . $competencia, 'Agrupado');
        } else {
            echo 'Formato n&atilde;o permitido';
        }
    }


    function excluir_lancamentos($mes = NULL)
    {

        $model = new ImportacaoModel();
        $model->where('competencia', $mes)->delete();

        return redirect()->to('lista/agrupado/' . $mes);

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
