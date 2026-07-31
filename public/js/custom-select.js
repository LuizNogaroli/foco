const CAMPO511_DATA = [
    {
        group: "Regimes de Destinação",
        options: [
            { value: "Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)", label: "Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)" },
            { value: "Aforamento gratuito", label: "Aforamento gratuito" },
            { value: "Aforamento oneroso", label: "Aforamento oneroso" },
            { value: "Arrendamento", label: "Arrendamento" },
            { value: "Autorização de obras", label: "Autorização de obras" },
            { value: "Autorização de passagem gratuita", label: "Autorização de passagem gratuita" },
            { value: "Autorização de passagem onerosa", label: "Autorização de passagem onerosa" },
            { value: "Autorização de uso para fins comerciais", label: "Autorização de uso para fins comerciais" },
            { value: "Autorização de uso sustentável", label: "Autorização de uso sustentável" },
            { value: "Cessão de uso em condições especiais", label: "Cessão de uso em condições especiais" },
            { value: "Cessão de uso gratuita", label: "Cessão de uso gratuita" },
            { value: "Cessão de uso onerosa", label: "Cessão de uso onerosa" },
            { value: "Cessão de uso provisória", label: "Cessão de uso provisória" },
            { value: "Concessão de Direito de Superfície Gratuita", label: "Concessão de Direito de Superfície Gratuita" },
            { value: "Concessão de Direito de Superfície Onerosa", label: "Concessão de Direito de Superfície Onerosa" },
            { value: "Concessão de Direito Real de Laje Gratuita", label: "Concessão de Direito Real de Laje Gratuita" },
            { value: "Concessão de Direito Real de Laje Onerosa", label: "Concessão de Direito Real de Laje Onerosa" },
            { value: "Concessão de Direito Real de Uso Gratuita", label: "Concessão de Direito Real de Uso Gratuita" },
            { value: "Concessão de Direito Real de Uso Onerosa", label: "Concessão de Direito Real de Uso Onerosa" },
            { value: "Concessão de uso especial para fins de moradia (CUEM)", label: "Concessão de uso especial para fins de moradia (CUEM)" },
            { value: "Dação em pagamento", label: "Dação em pagamento" },
            { value: "Declaração de Interesse do Serviço Publico", label: "Declaração de Interesse do Serviço Publico" },
            { value: "Doação", label: "Doação" },
            { value: "Entrega", label: "Entrega" },
            { value: "Entrega provisória", label: "Entrega provisória" },
            { value: "Guarda Provisória", label: "Guarda Provisória" },
            { value: "Inscrição de ocupação", label: "Inscrição de ocupação" },
            { value: "Integralização de cotas em Fundo de Investimento Imobiliário", label: "Integralização de cotas em Fundo de Investimento Imobiliário" },
            { value: "Investidura", label: "Investidura" },
            { value: "Locação para terceiros", label: "Locação para terceiros" },
            { value: "Permissão de uso para eventos de curta duração", label: "Permissão de uso para eventos de curta duração" },
            { value: "Permissão de uso para fins residenciais", label: "Permissão de uso para fins residenciais" },
            { value: "Permuta", label: "Permuta" },
            { value: "Promessa de compra e venda", label: "Promessa de compra e venda" },
            { value: "Remição do foro", label: "Remição do foro" },
            { value: "Transferência de gestão de orlas e praias", label: "Transferência de gestão de orlas e praias" },
            { value: "Transferência de direito real de uso para Reurb-S", label: "Transferência de direito real de uso para Reurb-S" },
            { value: "Transferência de propriedade para fins de Reurb-S", label: "Transferência de propriedade para fins de Reurb-S" },
            { value: "Transferência gratuita da posse", label: "Transferência gratuita da posse" },
            { value: "Transferência onerosa da posse", label: "Transferência onerosa da posse" },
            { value: "Venda", label: "Venda" }
        ]
    }
];

function initCustomSelect(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    // Sincroniza as opções do select nativo com a fonte de verdade (CAMPO511_DATA)
    select.innerHTML = '<option value="">Selecione um regime...</option>';
    CAMPO511_DATA.forEach(group => {
        group.options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            select.appendChild(option);
        });
    });

    select.style.display = 'none';

    const wrapper = document.createElement('div');
    wrapper.className = 'cs-wrapper';
    wrapper.dataset.csFor = selectId;

    // Display (trigger)
    const display = document.createElement('div');
    display.className = 'cs-display';
    display.setAttribute('tabindex', '0');
    display.setAttribute('role', 'combobox');
    display.setAttribute('aria-haspopup', 'listbox');
    display.setAttribute('aria-expanded', 'false');

    const placeholder = document.createElement('span');
    placeholder.className = 'cs-placeholder';
    placeholder.textContent = 'Selecione um regime...';

    const arrow = document.createElement('span');
    arrow.className = 'cs-arrow';
    arrow.textContent = '▼';

    display.appendChild(placeholder);
    display.appendChild(arrow);

    // Dropdown
    const dropdown = document.createElement('div');
    dropdown.className = 'cs-dropdown';
    dropdown.setAttribute('role', 'listbox');

    // Popula grupos e opções (sem ícones)
    CAMPO511_DATA.forEach((group, groupIndex) => {
        const header = document.createElement('div');
        header.className = groupIndex === 0 ? 'cs-group-header first' : 'cs-group-header';
        header.textContent = group.group;
        dropdown.appendChild(header);

        group.options.forEach(opt => {
            const item = document.createElement('div');
            item.className = 'cs-option';
            item.setAttribute('role', 'option');
            item.setAttribute('data-value', opt.value);
            item.textContent = opt.label;

            item.addEventListener('click', (e) => {
                e.stopPropagation();
                selectOption(item, opt.value, opt.label, wrapper, display, selectId);
            });

            dropdown.appendChild(item);
        });
    });

    wrapper.appendChild(display);
    wrapper.appendChild(dropdown);

    select.parentNode.insertBefore(wrapper, select.nextSibling);

    // Abrir/fechar ao clicar no display
    display.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown(wrapper);
    });

    // Suporte a teclado
    display.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleDropdown(wrapper);
        } else if (e.key === 'Escape') {
            closeDropdown(wrapper);
        }
    });

    // Fecha ao clicar fora
    document.addEventListener('click', () => closeDropdown(wrapper));

    // Validação visual
    select.addEventListener('invalid', () => wrapper.classList.add('invalid'));
    select.addEventListener('change', () => {
        if (select.value) wrapper.classList.remove('invalid');
    });

    // Restaurar valor inicial (data-selected ou selected nativo)
    const initialValue = select.getAttribute('data-selected') || select.value;
    if (initialValue) {
        const matchingItem = Array.from(dropdown.querySelectorAll('.cs-option'))
                                  .find(item => item.getAttribute('data-value') === initialValue);
        if (matchingItem) {
            // Emula o clique apenas para refletir visualmente no custom select,
            // sem disparar múltiplos change events na inicialização.
            const label = matchingItem.textContent;
            wrapper.querySelectorAll('.cs-option').forEach(o => o.classList.remove('selected'));
            matchingItem.classList.add('selected');
            const textEl = display.querySelector('.cs-placeholder, .cs-selected-text');
            textEl.className = 'cs-selected-text';
            textEl.textContent = label;
            select.value = initialValue;
            triggerObsBlock(initialValue);
        }
    }
}

function toggleDropdown(wrapper) {
    if (wrapper.classList.contains('disabled')) return;
    const isOpen = wrapper.classList.contains('open');

    // Fecha todos os outros
    document.querySelectorAll('.cs-wrapper.open').forEach(w => {
        w.classList.remove('open');
        w.querySelector('.cs-display')?.setAttribute('aria-expanded', 'false');
    });

    if (!isOpen) {
        wrapper.classList.add('open');
        wrapper.querySelector('.cs-display').setAttribute('aria-expanded', 'true');
    }
}

function closeDropdown(wrapper) {
    wrapper.classList.remove('open');
    wrapper.querySelector('.cs-display')?.setAttribute('aria-expanded', 'false');
}

function selectOption(item, value, label, wrapper, display, selectId) {
    // Marca opção selecionada
    wrapper.querySelectorAll('.cs-option').forEach(o => o.classList.remove('selected'));
    item.classList.add('selected');

    // Atualiza texto do display
    const textEl = display.querySelector('.cs-placeholder, .cs-selected-text');
    textEl.className = 'cs-selected-text';
    textEl.textContent = label;

    // Sincroniza com o <select> nativo
    const select = document.getElementById(selectId);
    if (select) {
        select.value = value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    wrapper.classList.remove('invalid');
    closeDropdown(wrapper);

    // Exibe bloco de observações, se existir
    triggerObsBlock(value);
}

function triggerObsBlock(value) {
    const bloco = document.getElementById('bloco511_obs');
    if (bloco) bloco.style.display = value ? 'block' : 'none';
}

// Inicializa ao carregar o DOM
document.addEventListener('DOMContentLoaded', () => {
    initCustomSelect('campo511');
});
