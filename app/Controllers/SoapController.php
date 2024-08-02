<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SoapController extends Controller
{
    public function sendRequest()
    {
        // URL do serviço SOAP
        $url = "https://crea-pr.implanta.net.br/siscont/servico/Integracao/Despesa.svc/soap1";

        // Envelope XML customizado
        $xmlEnvelope = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:imp="http://schemas.datacontract.org/2004/07/Implanta.Siscont.Business.Service.Integracao.Entity">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:IncluirSolicitacaoReservaOrcamentaria>
         <tem:solicitacao>
            <imp:ContaContabil>6.2.2.1.1.01.01.01.001</imp:ContaContabil>
            <imp:DistribuicoesCentroCusto>
               <imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>
                  <imp:CentroCustoCodigo>1.001</imp:CentroCustoCodigo>
                  <imp:Valor>50</imp:Valor>
               </imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>
               <imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>
                  <imp:CentroCustoCodigo>1.002</imp:CentroCustoCodigo>
                  <imp:Valor>65</imp:Valor>
               </imp:SolicitacoesReservasOrcamentariasCentroCustoEntity>
            </imp:DistribuicoesCentroCusto>
            <imp:EmpenhoData>2024-08-01</imp:EmpenhoData>
            <imp:EmpenhoTipo>Ordinario</imp:EmpenhoTipo>
            <imp:EmpenhoValor>115</imp:EmpenhoValor>
            <imp:FavorecidoCPFCNPJ>76639384000159</imp:FavorecidoCPFCNPJ>
            <imp:FavorecidoNome>Folha Pagamento CREA-PR</imp:FavorecidoNome>
            <imp:Historico>Despesa com pagamento de salários a funcionários, durante o exercício de 2024.</imp:Historico>
            <imp:Justificativa>Teste justificativa</imp:Justificativa>
            <imp:NumeroProcesso>017.003078/2023-00</imp:NumeroProcesso>
            <imp:SolicitacaoTipo>Empenho</imp:SolicitacaoTipo>
         </tem:solicitacao>
      </tem:IncluirSolicitacaoReservaOrcamentaria>
   </soapenv:Body>
</soapenv:Envelope>
XML;

        try {
            // Defina os cabeçalhos HTTP
            $headers = [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tempuri.org/IDespesa/IncluirSolicitacaoReservaOrcamentaria"',
                'Accept: */*'
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
            echo "<pre>";
            echo "HTTP Code: " . $httpCode . "\n";
            echo "Response: \n" . htmlspecialchars($response) . "\n";
            echo "</pre>";

            // Log response
            log_message('debug', 'SOAP Response: ' . $response);
        } catch (\Exception $e) {
            // Trate erros genéricos
            echo "Erro: " . $e->getMessage();
            log_message('error', 'Erro: ' . $e->getMessage());
        }
    }
}
