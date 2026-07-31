const fs = require('fs');
const file = 'c:/dev/Foco-16/resources/views/processos/abas/aba3.blade.php';
let content = fs.readFileSync(file, 'utf8');

// Find campo511
const campo511Index = content.indexOf('id="campo511"');
if(campo511Index > -1) {
  const selectStr = '<option value="">Selecione...</option>';
  const index = content.indexOf(selectStr, campo511Index);
  if(index > -1 && index < campo511Index + 300) {
    content = content.substring(0, index) + '<option value="">Selecione um regime...</option>' + content.substring(index + selectStr.length);
    fs.writeFileSync(file, content);
    console.log('Success');
  } else {
    console.log('Option not found');
  }
} else {
  console.log('campo511 not found');
}
