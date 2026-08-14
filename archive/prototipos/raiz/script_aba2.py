import os

filepath = r'c:\dev\Foco-17\resources\views\processos\abas\aba2.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Aba 1a
old_1 = '''      <div class="accordion-item" id="acc_aba1a" style="border: 2px solid #1e3a5f; border-radius: 8px; overflow: hidden;">
        <div class="accordion-header" style="background-color: #1e3a5f; color: white; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 1.1em;" onclick="const body = this.nextElementSibling; const icon = this.querySelector('span:last-child'); if(body.style.display === 'none'){ body.style.display = 'block'; icon.style.transform = 'rotate(180deg)'; } else { body.style.display = 'none'; icon.style.transform = 'rotate(0deg)'; }">
          <span>📋 Dados do Requerimento</span>
          <span style="transition: transform 0.3s; font-size: 1.2em;">▼</span>
        </div>
        <div class="accordion-body" style="padding: 20px; display: none; border-top: 1px solid #cbd5e1; background: #fff;">'''

new_1 = '''      <div class="accordion-item" id="acc_aba1a" style="border: none;">
        <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
          <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📋 Dados do Requerimento</span>
          <span class="accordion-icon">▶</span>
        </div>
        <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">'''
content = content.replace(old_1, new_1)

# 2. Aba 1b
old_2 = '''      <div class="accordion-item" id="acc_aba1b" style="border: 2px solid #1e3a5f; border-radius: 8px; overflow: hidden;">
        <div class="accordion-header" style="background-color: #1e3a5f; color: white; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 1.1em;" onclick="const body = this.nextElementSibling; const icon = this.querySelector('span:last-child'); if(body.style.display === 'none'){ body.style.display = 'block'; icon.style.transform = 'rotate(180deg)'; } else { body.style.display = 'none'; icon.style.transform = 'rotate(0deg)'; }">
          <span>📍 RIP(s) ou Cadastro(s) Mínimo(s)</span>
          <span style="transition: transform 0.3s; font-size: 1.2em;">▼</span>
        </div>
        <div class="accordion-body" style="padding: 20px; display: none; border-top: 1px solid #cbd5e1; background: #fff;">'''

new_2 = '''      <div class="accordion-item" id="acc_aba1b" style="border: none;">
        <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
          <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📍 RIP(s) ou Cadastro(s) Mínimo(s)</span>
          <span class="accordion-icon">▶</span>
        </div>
        <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">'''
content = content.replace(old_2, new_2)

# 3. Imóvel RIP inline
old_3 = '''                block.style.cssText = 'background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:8px;';
                block.innerHTML = `<div style="background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;" onclick="const b=this.nextElementSibling;const i=this.querySelector('span:last-child');if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}else{b.style.display='none';i.style.transform='rotate(0deg)';}"><span>🏠 Imóvel (RIP): ${rip}</span><span style="transition:transform 0.2s;">▼</span></div>
                <div style="padding:16px;display:none;background:#fff;"><div style="display:flex;flex-direction:column;">'''

new_3 = '''                block.className = 'accordion-item';
                block.style.cssText = 'border:none;margin-bottom:8px;';
                block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">🏠 Imóvel (RIP): ${rip}</span>
                    <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"><div style="display:flex;flex-direction:column;">'''
content = content.replace(old_3, new_3)

# 4. Cadastro Mínimo inline
old_4 = '''                block.style.cssText = 'background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:8px;';
                block.innerHTML = `<div style="background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;" onclick="const b=this.nextElementSibling;const i=this.querySelector('span:last-child');if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}else{b.style.display='none';i.style.transform='rotate(0deg)';}"><span>📝 Cadastro Mínimo #${idx+1} (Sem RIP)</span><span style="transition:transform 0.2s;">▼</span></div>
                <div style="padding:16px;display:none;background:#fff;"><div style="display:flex;flex-direction:column;">'''

new_4 = '''                block.className = 'accordion-item';
                block.style.cssText = 'border:none;margin-bottom:8px;';
                block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📝 Cadastro Mínimo #${idx+1} (Sem RIP)</span>
                    <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"><div style="display:flex;flex-direction:column;">'''
content = content.replace(old_4, new_4)

# 5. Pessoas Associadas inline
old_5 = '''                <div style="background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;" onclick="const b=this.nextElementSibling;const i=this.querySelector('span:last-child');if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}else{b.style.display='none';i.style.transform='rotate(0deg)';}">
                    <span>👤 Pessoas Associadas ao RIP ${rip}</span><span style="transition:transform 0.2s;">▼</span>
                </div>
                <div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">'''

new_5_safe = '''                <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">👤 Pessoas Associadas ao RIP ${rip}</span>
                    <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">'''
content = content.replace(old_5, new_5_safe)

# 6. Benfeitorias inline
old_6 = '''                <div style="background:#e2e8f0;color:#1e293b;padding:12px 16px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:0.95em;" onclick="const b=this.nextElementSibling;const i=this.querySelector('span:last-child');if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}else{b.style.display='none';i.style.transform='rotate(0deg)';}">
                    <span>🏗️ Benfeitorias Associadas ao RIP ${rip}</span><span style="transition:transform 0.2s;">▼</span>
                </div>
                <div style="padding:16px;display:none;background:#fff;border-top:1px solid #cbd5e1;">'''

new_6_safe = '''                <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">🏗️ Benfeitorias Associadas ao RIP ${rip}</span>
                    <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">'''
content = content.replace(old_6, new_6_safe)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
