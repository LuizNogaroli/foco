import os, re

filepath = r'c:\dev\Foco-17\resources\views\processos\abas\aba3.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the span with transition
content = re.sub(r'<span style="transition: transform 0\.3s; font-size: 1\.2em;">▶</span>', r'<span class="accordion-icon">▶</span>', content)

# Fix outer divs of Aba 1a, 1b, Aba 2
content = content.replace('<div style="border: 2px solid #1e3a5f; border-radius: 8px; overflow: hidden; background: #fff;">', '<div class="accordion-item" style="border: none;">')

# Fix bodies of Aba 1a, 1b, Aba 2
content = content.replace('<div style="padding: 20px; display: none; border-top: 1px solid #cbd5e1; background: #fff;">', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">')

# Add class="accordion-title" to the spans containing the text
content = re.sub(r'<span>(📋 .*?)</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span>', content)
content = re.sub(r'<span>(📍 .*?)</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span>', content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done!')
