import os, re

filepath = r'c:\dev\Foco-17\resources\views\processos\abas\aba3.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Substituir os divs principais com background-color: #1e3a5f;
pattern_main = re.compile(r'<div style=\"background-color: #1e3a5f; color: white; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 1.1em;\" onclick=\"const body = this\.nextElementSibling; const icon = this\.querySelector\(\'span:last-child\'\); if\(body\.style\.display === \'none\'\){ body\.style\.display = \'block\'; icon\.style\.transform = \'rotate\(90deg\)\'; } else { body\.style\.display = \'none\'; icon\.style\.transform = \'rotate\(0deg\)\'; }\">')
new_main_header = '<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">'
content = pattern_main.sub(new_main_header, content)

# 2. Substituir os divs inline (background:#e2e8f0;color:#1e293b;) injetados no JS
pattern_inline = re.compile(r'<div style=\"background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;\" onclick=\"const b=this\.nextElementSibling;const i=this\.querySelector\(\'span:last-child\'\);if\(b\.style\.display===\'none\'\){b\.style\.display=\'block\';i\.style\.transform=\'rotate\(90deg\)\';}else{b\.style\.display=\'none\';i\.style\.transform=\'rotate\(0deg\)\';}\">')
new_inline_header = '<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">'
content = pattern_inline.sub(new_inline_header, content)

# 3. Fix old span arrows (▼)
content = content.replace('<span style="transition: transform 0.3s; font-size: 1.2em;">▼</span>', '<span class="accordion-icon">▶</span>')
# Also the inline ones (▶) that had transition (from my previous global replace script that changed -90deg to 90deg, the arrow in inline JS was actually ▶ or ▼ ?)
# Wait, my previous python script that did -90deg -> 90deg did NOT change the character from ▼ to ▶ in aba3!
# Let's replace both just in case:
content = content.replace('<span style="transition:transform 0.2s;">▼</span>', '<span class="accordion-icon">▶</span>')
content = content.replace('<span style="transition:transform 0.2s;">▶</span>', '<span class="accordion-icon">▶</span>')

# 4. Wrap titles in accordion-title
content = re.sub(r'<span>(📋 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(📍 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(📄 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(🏠 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(📝 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(👤 .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)
content = re.sub(r'<span>(🏗️ .*?)</span>\s*<span class="accordion-icon">▶</span>', r'<span class="accordion-title" style="font-weight: 600; color: #ffffff;">\1</span><span class="accordion-icon">▶</span>', content)

# 5. Fix the bodies (the outer divs that enclose the content)
# Main bodies: <div class="accordion-body" style="padding: 20px; display: none; border-top: 1px solid #cbd5e1; background: #fff;">
content = content.replace('<div class="accordion-body" style="padding: 20px; display: none; border-top: 1px solid #cbd5e1; background: #fff;">', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">')

# Inline bodies: <div style="padding:16px;display:none;background:#fff;"><div style="display:flex;flex-direction:column;">
content = content.replace('<div style="padding:16px;display:none;background:#fff;"><div style="display:flex;flex-direction:column;">', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"><div style="display:flex;flex-direction:column;">')

# Inline bodies (Pessoas/Benfeitorias): <div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">
content = content.replace('<div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">', '<div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">')

# 6. Make sure accordion-items exist or wrap headers properly.
# The main ones already have <div class="accordion-item" id="..." style="...">
content = content.replace('style="border: 2px solid #1e3a5f; border-radius: 8px; overflow: hidden;"', 'style="border: none;"')
# Remove block.style.cssText border styling for inline items since we apply styling on header/body now
content = content.replace("block.style.cssText = 'background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:8px;';", "block.className = 'accordion-item';\n                block.style.cssText = 'border:none;margin-bottom:8px;';")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated aba3")
