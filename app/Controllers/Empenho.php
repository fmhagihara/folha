<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ImportacaoModel;
use DateTime;
use SimpleXMLElement;

class Empenho extends Controller
{
   public function enviar($mes, $idgrupo)
   {
      $model = new ImportacaoModel();
      $agrupado = $model->agruparCentroCusto($mes);

      $date = new DateTime($mes);

      // Define o dia para o último dia do mês
      $date->modify('last day of this month');

      // Obtém a data formatada como string
      $ultimoDiaDoMes = $date->format('Y-m-d');

      foreach ($agrupado as $ag) :
         if ($ag['id_grupo'] == $idgrupo) {
            $ccusto = substr($ag['centrodecusto'], 0, 4);
            $empenho['conta'] = $ag['conta_empenho'];
            $empenho['nomeconta'] = $ag['nome_grupo'];
            if (isset($empenho['total_empenho'])) $empenho['total_empenho'] += $ag['soma'];
            else $empenho['total_empenho'] = $ag['soma'];
            if (isset($subprograma[$ccusto])) $subprograma[$ccusto] += $ag['soma'];
            else $subprograma[$ccusto] = $ag['soma'];
         }
      endforeach;

      if (isset($empenho, $subprograma)) {
       //  var_dump($empenho);
         //var_dump($subprograma);


      // URL do serviço SOAP
      $url = "https://crea-pr.implanta.net.br/siscont/servico/Integracao/Despesa.svc/soap1";

      // Envelope XML customizado
      $xmlEnvelope =
'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:imp="http://schemas.datacontract.org/2004/07/Implanta.Siscont.Business.Service.Integracao.Entity">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:IncluirSolicitacaoReservaOrcamentaria>
         <tem:solicitacao>
            <imp:ContaContabil>' . $empenho['conta'] . '</imp:ContaContabil>
            <imp:DistribuicoesCentroCusto>';
            foreach ($subprograma as $key=>$value) :
               $xmlEnvelope .= '
               <imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>
                  <imp:CentroCustoCodigo>' . substr($key, 0, 1) . '.' . substr($key, 1, 3) . '</imp:CentroCustoCodigo>
                  <imp:Valor>' . $value . '</imp:Valor>
               </imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>';
            endforeach;
            $xmlEnvelope .= '
            </imp:DistribuicoesCentroCusto>
            <imp:EmpenhoData>' . $ultimoDiaDoMes . '</imp:EmpenhoData>
            <imp:EmpenhoTipo>Ordinario</imp:EmpenhoTipo>
            <imp:EmpenhoValor>' . $empenho['total_empenho'] . '</imp:EmpenhoValor>
            <imp:FavorecidoCPFCNPJ>76639384000159</imp:FavorecidoCPFCNPJ>
            <imp:FavorecidoNome>Folha Pagamento CREA-PR</imp:FavorecidoNome>
            <imp:Historico>Despesa com pagamento de verbas salariais em ' . substr($mes, 5, 2) . '/' . substr($mes, 0, 4) . '.</imp:Historico>
            <imp:Justificativa>Automático folha</imp:Justificativa>
            <imp:NumeroProcesso>017.003078/2023-00</imp:NumeroProcesso>
            <imp:SolicitacaoTipo>Empenho</imp:SolicitacaoTipo>
         </tem:solicitacao>
      </tem:IncluirSolicitacaoReservaOrcamentaria>
   </soapenv:Body>
</soapenv:Envelope>';
//echo '<pre>' . print_r($xmlEnvelope) . '</pre>';
      }


      try {
         // Defina os cabeçalhos HTTP
         $headers = [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "http://tempuri.org/IDespesa/IncluirSolicitacaoReservaOrcamentaria"'
         ];

         // Inicie a sessão cURL
         $ch = curl_init();

         // Defina as opções cURL
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlEnvelope);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Permitir redirecionamentos
         curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Máximo de redirecionamentos
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Desabilitar verificação SSL do host
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitar verificação SSL do certificado

         // Defina uma opção para verificar a resposta do servidor
         curl_setopt($ch, CURLOPT_HEADER, true); // Inclua os cabeçalhos na saída

         // Execute a requisição cURL
         $response = curl_exec($ch);

         // Verifique se há erros na requisição cURL
         if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new \Exception($error_msg);
         }

         // Obtenha informações sobre a resposta
         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

         // Feche a sessão cURL
         curl_close($ch);

         // Log request
         log_message('debug', 'SOAP Request: ' . $xmlEnvelope);
         log_message('debug', 'SOAP Request Headers: ' . json_encode($headers));

         // Faça algo com a resposta
         if ($httpCode == 200) {
            $xml = new SimpleXMLElement($response);

            // Registrar os namespaces para acessar elementos qualificados
            $namespaces = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('a', $namespaces['a']);

            // Extrair o valor do nó <a:Entity>
            $entity = $xml->xpath('//a:Entity')[0];
            $entityValue = (string)$entity;
            echo 'Solicitação de Reserva Orçamentária gerada com sucesso! ID: ' . $entityValue;
         }
         /*
         echo "<pre>";
         echo "HTTP Code: " . $httpCode . "\n";
         echo "Response: \n" . htmlspecialchars($response) . "\n";
         echo "</pre>";
*/
         // Log response
         log_message('debug', 'SOAP Response: ' . $response);
      } catch (\Exception $e) {
         // Trate erros genéricos
         echo "Erro: " . $e->getMessage();
         log_message('error', 'Erro: ' . $e->getMessage());
      }

   }
}
