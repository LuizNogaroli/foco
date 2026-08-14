const fs = require('fs');
const file = 'c:/dev/Foco-16/resources/views/processos/abas/aba3.blade.php';
let content = fs.readFileSync(file, 'utf8');
const startMarker = '<option value="">Selecione...</option>';
const endMarker = '</select>';
const startIndex = content.indexOf(startMarker);
const endIndex = content.indexOf(endMarker, startIndex);

if(startIndex > -1 && endIndex > -1) {
  const newOptions = `            <option value="">Selecione...</option>
            <option value="Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)">Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)</option>
            <option value="Aforamento gratuito">Aforamento gratuito</option>
            <option value="Aforamento oneroso">Aforamento oneroso</option>
            <option value="Arrendamento">Arrendamento</option>
            <option value="Autorização de obras">Autorização de obras</option>
            <option value="Autorização de passagem gratuita">Autorização de passagem gratuita</option>
            <option value="Autorização de passagem onerosa">Autorização de passagem onerosa</option>
            <option value="Autorização de uso para fins comerciais">Autorização de uso para fins comerciais</option>
            <option value="Autorização de uso sustentável">Autorização de uso sustentável</option>
            <option value="Cessão de uso em condições especiais">Cessão de uso em condições especiais</option>
            <option value="Cessão de uso gratuita">Cessão de uso gratuita</option>
            <option value="Cessão de uso onerosa">Cessão de uso onerosa</option>
            <option value="Cessão de uso provisória">Cessão de uso provisória</option>
            <option value="Concessão de Direito de Superfície Gratuita">Concessão de Direito de Superfície Gratuita</option>
            <option value="Concessão de Direito de Superfície Onerosa">Concessão de Direito de Superfície Onerosa</option>
            <option value="Concessão de Direito Real de Laje Gratuita">Concessão de Direito Real de Laje Gratuita</option>
            <option value="Concessão de Direito Real de Laje Onerosa">Concessão de Direito Real de Laje Onerosa</option>
            <option value="Concessão de Direito Real de Uso Gratuita">Concessão de Direito Real de Uso Gratuita</option>
            <option value="Concessão de Direito Real de Uso Onerosa">Concessão de Direito Real de Uso Onerosa</option>
            <option value="Concessão de uso especial para fins de moradia (CUEM)">Concessão de uso especial para fins de moradia (CUEM)</option>
            <option value="Dação em pagamento">Dação em pagamento</option>
            <option value="Declaração de Interesse do Serviço Publico">Declaração de Interesse do Serviço Publico</option>
            <option value="Doação">Doação</option>
            <option value="Entrega">Entrega</option>
            <option value="Entrega provisória">Entrega provisória</option>
            <option value="Guarda Provisória">Guarda Provisória</option>
            <option value="Inscrição de ocupação">Inscrição de ocupação</option>
            <option value="Integralização de cotas em Fundo de Investimento Imobiliário">Integralização de cotas em Fundo de Investimento Imobiliário</option>
            <option value="Investidura">Investidura</option>
            <option value="Locação para terceiros">Locação para terceiros</option>
            <option value="Permissão de uso para eventos de curta duração">Permissão de uso para eventos de curta duração</option>
            <option value="Permissão de uso para fins residenciais">Permissão de uso para fins residenciais</option>
            <option value="Permuta">Permuta</option>
            <option value="Promessa de compra e venda">Promessa de compra e venda</option>
            <option value="Remição do foro">Remição do foro</option>
            <option value="Transferência de gestão de orlas e praias">Transferência de gestão de orlas e praias</option>
            <option value="Transferência de direito real de uso para Reurb-S">Transferência de direito real de uso para Reurb-S</option>
            <option value="Transferência de propriedade para fins de Reurb-S">Transferência de propriedade para fins de Reurb-S</option>
            <option value="Transferência gratuita da posse">Transferência gratuita da posse</option>
            <option value="Transferência onerosa da posse">Transferência onerosa da posse</option>
            <option value="Venda">Venda</option>
          `;
  content = content.substring(0, startIndex) + newOptions + content.substring(endIndex);
  fs.writeFileSync(file, content);
  console.log('Replacement successful');
} else {
  console.log('Markers not found');
}
