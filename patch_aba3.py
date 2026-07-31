import re

path = r'c:\dev\Foco-17\resources\views\processos\abas\aba3.blade.php'

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add HTMX container and modify form
content = re.sub(
    r'<div class="form-container">',
    r'<div id="aba3-container" class="form-container">',
    content
)

content = re.sub(
    r'<form method="POST" action="\{\{ route\(\'processos\.tramitar\', \$processo->id\) \}\}" id="form03">',
    r'<form hx-post="{{ route(\'processos.tramitar\', $processo->id) }}" hx-target="#aba3-container" hx-indicator="#form-indicator-aba3" id="form03">\n      <div id="form-indicator-aba3" class="htmx-indicator" style="display:none; color: #475569; margin-bottom: 10px;">⏳ Processando...</div>',
    content
)

# 2. Update Devolver buttons to use HTMX
devolver_pattern = r'<div style="display: flex; gap: 15px; flex-wrap: wrap;">\s*<button type="button" class="btnEnviarDevolucaoRapida" data-workflow="12"[^>]*>🔙 Indicação do Imóvel</button>\s*<button type="button" class="btnEnviarDevolucaoRapida" data-workflow="13"[^>]*>🔙 Diagnóstico Preliminar</button>\s*</div>'

devolver_replacement = r'''<div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button type="button" 
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-vals='{"aba": 1}' 
                        hx-include="#motivo_devolucao_rapida, input[name='_token']" 
                        hx-target="#aba3-container"
                        hx-indicator="#form-indicator-aba3"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #be123c; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(190, 18, 60, 0.2);">
                    🔙 Indicação do Imóvel
                </button>
                <button type="button" 
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-vals='{"aba": 2}' 
                        hx-include="#motivo_devolucao_rapida, input[name='_token']" 
                        hx-target="#aba3-container"
                        hx-indicator="#form-indicator-aba3"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #9f1239; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(159, 18, 57, 0.2);">
                    🔙 Diagnóstico Preliminar
                </button>
              </div>'''
content = re.sub(devolver_pattern, devolver_replacement, content, flags=re.DOTALL)

# Remove the inline script for devolver
devolver_script_pattern = r'<script>\s*document\.querySelectorAll\(\'\.btnEnviarDevolucaoRapida\'\).*?}\);?\s*</script>'
content = re.sub(devolver_script_pattern, '', content, flags=re.DOTALL)


# 3. Update Salvar e Enviar button and remove modal invocation
salvar_pattern = r'<button type="button" class="btn-action" style="width: 48%;[^"]*" onclick="showModalAprovacao\(\)">💾 Salvar e Enviar</button>'
salvar_replacement = r'<button type="submit" class="btn-action" style="width: 48%; font-size: 1.2em; padding: 16px; background-color: #0284c7; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;">💾 Salvar e Enviar</button>'
content = re.sub(salvar_pattern, salvar_replacement, content)


# 4. Remove the huge JS block for Supabase/Modal
js_block_pattern = r'let ultimoRelatorioSalvoAba3 = \{\};\s*async function executarSalvamentoAba3\(\) \{.*?(?=</script>\s*</body>\s*</html>)'
content = re.sub(js_block_pattern, '', content, flags=re.DOTALL)

# 5. Remove the modal html if it exists inside aba3.blade.php
# wait, we couldn't find "modalAprovacaoAba3" html in the file earlier. It might have been in aba2.blade.php or somewhere else.
# But just in case, we will look for it.
modal_pattern = r'<!-- Modal Aprovação Aba 3 -->.*?</div>\s*</div>'
content = re.sub(modal_pattern, '', content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Applied HTMX migration to aba3.blade.php")
