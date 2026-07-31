import os, re

filepath = r'c:\dev\Foco-17\resources\views\processos\abas\aba2.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# The pattern for inline JS div header
pattern = re.compile(r'<div style=\"background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;\" onclick=\"const b=this\.nextElementSibling;const i=this\.querySelector\(\'span:last-child\'\);if\(b\.style\.display===\'none\'\){b\.style\.display=\'block\';i\.style\.transform=\'rotate\(180deg\)\';}else{b\.style\.display=\'none\';i\.style\.transform=\'rotate\(0deg\)\';}\">')

new_header = '<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">'
content = pattern.sub(new_header, content)

# Replace old spans
content = content.replace('<span style="transition:transform 0.2s;">▼</span>', '<span class="accordion-icon">▶</span>')

# Update title span
content = re.sub(r'<span>(.*?)</span><span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)

# Fix body blocks
content = content.replace('<div style="padding:16px;display:none;background:#fff;"', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"')

# There might also be bodies like <div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">
content = content.replace('<div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print('Done!')
