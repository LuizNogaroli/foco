import re

path = r'c:\dev\Foco-17\app\Http\Controllers\ProcessoController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add the helper method
helper = '''
    private function htmxRedirect($request, $redirectResponse)
    {
        if ($request->header('HX-Request')) {
            $redirectResponse->setStatusCode(200);
            $redirectResponse->header('HX-Redirect', $redirectResponse->getTargetUrl());
            $redirectResponse->setContent('');
        }
        return $redirectResponse;
    }

    public function tramitar'''

content = content.replace('    public function tramitar', helper)

# 2. Replace back()->withErrors(...) calls
content = re.sub(
    r'return back\(\)->withErrors\(\[(.*?)\]\)->withInput\(\);',
    r"return $this->htmxRedirect($request, back()->withErrors([\1])->withInput());",
    content
)

# 3. Replace the final returns in tramitar
# Specifically:
# if ($nextAba === 'index') {
#     if ($request->header('HX-Request')) {
#         session()->flash('success', 'Processo salvo com sucesso!');
#         return response('', 200)->header('HX-Redirect', route('processos.index'));
#     }
#     return redirect()->route('processos.index')
#                      ->with('success', 'Processo salvo com sucesso!');
# }
# return redirect()->route('processos.show', ['processo' => $processo->id, 'aba' => $nextAba])
#                  ->with('success', 'Formulário salvo com sucesso!');

old_end_pattern = r"if \(\$nextAba === 'index'\) \{\s*if \(\$request->header\('HX-Request'\)\) \{\s*session\(\)->flash\('success', 'Processo salvo com sucesso!'\);\s*return response\('', 200\)->header\('HX-Redirect', route\('processos.index'\)\);\s*\}\s*return redirect\(\)->route\('processos\.index'\)\s*->with\('success', 'Processo salvo com sucesso!'\);\s*\}\s*return redirect\(\)->route\('processos\.show', \['processo' => \$processo->id, 'aba' => \$nextAba\]\)\s*->with\('success', 'Formulário salvo com sucesso!'\);"

new_end = """if ($nextAba === 'index') {
            return $this->htmxRedirect($request, redirect()->route('processos.index')->with('success', 'Processo salvo com sucesso!'));
        }

        return $this->htmxRedirect($request, redirect()->route('processos.show', ['processo' => $processo->id, 'aba' => $nextAba])->with('success', 'Formulário salvo com sucesso!'));"""

content = re.sub(old_end_pattern, new_end, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("ProcessoController patched.")
