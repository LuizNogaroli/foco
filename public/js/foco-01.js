// foco-01.js
// Exclusivo da Seção 1: Dados do Requerimento (foco-01.html)
// Depende de: formulario.js (mascaraCPFCNPJ, mascaraSEI)

window.ripsPesquisados = window.ripsPesquisados || {};
window.imovelCount = window.imovelCount || 0;

function inicializarFoco01() {
    const form01 = document.getElementById('form01');
    if (!form01) return;

    // =============================="é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é
    // 1. MODAL DE SIMULAé!éO (Prioridade para interatividade)
    // =============================="é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é
    const modalSim = document.getElementById('modalSimulacao');
    const btnFecharSim = document.getElementById('btnFecharSimulacao');
    
    // Seleciona todos os botões que devem abrir o popup
    const btnsAbrir = document.querySelectorAll('.btn-simular-doc');

    btnsAbrir.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = this.getAttribute('data-url');
            if (url && url !== '#') {
                window.open(url, '_blank');
            } else if (modalSim) {
                modalSim.style.display = 'flex';
            }
        });
    });

    if (btnFecharSim) {
        btnFecharSim.addEventListener('click', function() {
            if (modalSim) modalSim.style.display = 'none';
        });
    }

    // =============================="é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é
    // 2. MéSCARAS E INICIALIZAé!éO
    // =============================="é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é

    if (typeof mascaraCPFCNPJ === 'function') mascaraCPFCNPJ(document.getElementById('campo14'));
    if (typeof mascaraSEI === 'function') mascaraSEI(document.getElementById('campo13'));

    // Méscara de telefone celular
    const inputTel = document.getElementById('campo19');
    if (inputTel) {
        inputTel.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').slice(0, 11);
            if (v.length <= 10) {
                v = v.replace(/(\d{2})(\d)/,       '($1) $2');
                v = v.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
            } else {
                v = v.replace(/(\d{2})(\d)/,       '($1) $2');
                v = v.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            }
            this.value = v;
        });
    }

    // =============================="é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é"é
    // PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP
    // 3. VALIDAééO E SUBMIT
    // PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP



    // PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP
    // 4. BOTéO LIMPAR
    // PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP

    const btnLimpar = document.getElementById('btnLimpar');
    if (btnLimpar) {
        btnLimpar.addEventListener('click', function () {
            if (confirm('Deseja limpar todos os campos?')) {
                form01.reset();
                document.querySelectorAll('#form01 .error-msg').forEach(e => e.style.display = 'none');
                document.querySelectorAll('#form01 input').forEach(el => el.style.borderColor = '');
                // Limpa também as seções condicionais
                if (typeof verificarConceituacao === 'function') verificarConceituacao();
                document.getElementById('imoveis-container').innerHTML = '';
                document.getElementById('listaRIPsAssociados').innerHTML = '';
                document.getElementById('listaRIPsAssociados').style.display = 'none';
                window.ripsPesquisados = {};
                window.imovelCount = 0;
            }
        });
    }

    // =========================================================================
    // 5. RENDERIZAÇÃO DINÂMICA DE DOCUMENTOS
    // =========================================================================
    // Fallback/Polling para evitar race conditions e atualizar título
    let docsRendered = false;
    const checkStateInterval = setInterval(() => {
        if (window.parent && window.parent.formDataState) {
            const data = window.parent.formDataState;
            const labelTitulo = document.getElementById('label-titulo-requerimento');
            if (labelTitulo) {
                labelTitulo.textContent = 'Requerimento';
            }
            const tituloPagina = document.getElementById('titulo-pagina-requerimento');
            /*
            if (tituloPagina) {
                tituloPagina.textContent = data.tipo_requerimento || data.procedimento || 'Regularizar Utilização de Imóvel da União';
            }
            */
            if (data.documentos_anexados) {
                clearInterval(checkStateInterval);
                if (!docsRendered) {
                    renderDocumentos(data.documentos_anexados);
                    docsRendered = true;
                }
            }
        }
    }, 200);

    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'DATABASE_LOADED') {
            const data = event.data.data;
            const labelTitulo = document.getElementById('label-titulo-requerimento');
            if (labelTitulo) {
                labelTitulo.textContent = 'Requerimento';
            }
            const tituloPagina = document.getElementById('titulo-pagina-requerimento');
            /*
            if (tituloPagina) {
                tituloPagina.textContent = data.tipo_requerimento || data.procedimento || 'Regularizar Utilização de Imóvel da União';
            }
            */
            if (data && data.documentos_anexados && Array.isArray(data.documentos_anexados)) {
                clearInterval(checkStateInterval);
                if (!docsRendered) {
                    renderDocumentos(data.documentos_anexados);
                    docsRendered = true;
                }
            }

            // Tratamento de Devolução
            if (data && data.status_devolucao === 'Aba 1') {
                const banner = document.getElementById('bannerDevolucaoAba1');
                const textoMotivo = document.getElementById('textoMotivoDevolucaoAba1');
                const blocoResposta = document.getElementById('blocoRespostaDevolutivaAba1');
                const inputResposta = document.getElementById('resposta_devolucao');
                
                if (banner && textoMotivo && blocoResposta && inputResposta) {
                    banner.style.display = 'block';
                    textoMotivo.textContent = data.motivo_devolucao || 'Motivo não especificado.';
                    
                    blocoResposta.style.display = 'block';
                    inputResposta.required = true;
                    // Preenche a resposta se já tiver salva no draft
                    if (data.resposta_devolucao) {
                        inputResposta.value = data.resposta_devolucao;
                    }
                }
            }
        }
    });

    function renderDocumentos(docs) {
        const tbody = document.getElementById('documentos-anexados-body');
        if (!tbody) return;
        
        if (docs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="2" style="text-align: center; padding: 15px; color: #64748b;">Nenhum documento anexado encontrado.</td></tr>`;
            return;
        }
        
        let html = '';
        docs.forEach(doc => {
            let icon = '📄';
            if (doc.nome && doc.nome.toLowerCase().includes('contrato')) icon = '🏢';
            else if (doc.nome && doc.nome.toLowerCase().includes('identificação')) icon = '🪪';
            else if (doc.nome && doc.nome.toLowerCase().includes('procuração')) icon = '📝';
            else if (doc.nome && doc.nome.toLowerCase().includes('comprovante')) icon = '🔎';
            
            html += `
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 8px;">
                        <div class="doc-nome" style="display: flex; align-items: center; gap: 8px;">
                            <span class="doc-icon">${icon}</span>
                            <div><strong>${doc.nome || 'Documento Anexado'}</strong></div>
                        </div>
                    </td>
                    <td class="coluna-acao" style="text-align: right; padding: 10px 8px;">
                        <button type="button" class="btn-link-doc btn-simular-doc" data-url="${doc.url || '#'}" style="background-color: #0284c7; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; display: inline-block;" title="Visualizar">👁️ Visualizar</button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        
        // Re-anexar listeners do modal nos novos botões
        const btnsAbrir = tbody.querySelectorAll('.btn-simular-doc');
        const modalSim = document.getElementById('modalSimulacao');
        btnsAbrir.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const url = this.getAttribute('data-url');
                if (url && url !== '#') {
                    window.open(url, '_blank');
                } else if (modalSim) {
                    modalSim.style.display = 'flex';
                }
            });
        });
    }

    // =========================================================================
    // 4. TOGGLE DOS BOTÕES DA CONCEITUAÇÃO DO IMÓVEL
    // =========================================================================

    // Toggle para botões Inserir RIP / Inserir Cadastro Mínimo com base no Select (Removido, usando menus novos)
    const btnEnviar = document.getElementById('btnEnviar');

    // Estado da solicitação de criação de RIP
    window.solicitacaoCriacaoRip = "";

    function atualizarLayoutConceituacao() {
        if (!btnEnviar) return;
        
        let habilitado = false;
        if ((window.ripsPendentes && window.ripsPendentes.length > 0) || window.solicitacaoCriacaoRip) {
            habilitado = true;
        } else if (window.cadastrosPendentes && window.cadastrosPendentes.length > 0) {
            habilitado = true;
        }

        if (habilitado) {
            btnEnviar.disabled = false;
            btnEnviar.style.opacity = "1";
            btnEnviar.style.pointerEvents = "auto";
            btnEnviar.style.cursor = "pointer";
        } else {
            btnEnviar.disabled = true;
            btnEnviar.style.opacity = "0.4";
            btnEnviar.style.pointerEvents = "none";
            btnEnviar.style.cursor = "not-allowed";
        }
    }

    // Lógica para seleção de conceituação através dos menus hover
    window.conceituacaoSelecionada = "";
    
    window.selecionarConceituacaoBotao = function(valor, tipoBotao) {
        window.conceituacaoSelecionada = valor;
        
        // Abre o respectivo modal diretamente
        if (tipoBotao === 'com_rip') {
            const modalRip = document.getElementById('modalInserirRip');
            if (modalRip) modalRip.style.display = 'flex';
            const inputRip = document.getElementById('inputNumeroRip');
            if (inputRip) {
                inputRip.value = '';
                inputRip.focus();
            }
        } else if (tipoBotao === 'sem_rip') {
            const modalCadastro = document.getElementById('modalCadastroMinimo');
            if (modalCadastro) {
                modalCadastro.style.display = 'flex';
                // Inicializa o mapa corretamente
                setTimeout(() => {
                    if (typeof initMap === 'function') initMap();
                }, 100);
            }
        }
        
        atualizarLayoutConceituacao();
    };

    // LÓGICA DO MODAL SOLICITAR CRIAÇÃO DE RIP
    console.log("🔍 [foco-01] Iniciando binding do modal de solicitação...");
    const modalSolicitacao = document.getElementById('modalSolicitarCriacaoRip');
    const btnSolicitar = document.getElementById('btnSolicitarCriacaoRip');
    const btnFecharSolicitacao = document.getElementById('btnFecharModalSolicitacaoRip');
    const btnCancelarSolicitacao = document.getElementById('btnCancelarSolicitacaoRip');
    const btnSalvarSolicitacao = document.getElementById('btnSalvarSolicitacaoRip');
    const inputSolicitacao = document.getElementById('inputSolicitacaoCriacao');

    console.log("🔍 [foco-01] Elementos consultados:", {
        modalSolicitacao: !!modalSolicitacao,
        btnSolicitar: !!btnSolicitar,
        btnFecharSolicitacao: !!btnFecharSolicitacao,
        btnCancelarSolicitacao: !!btnCancelarSolicitacao,
        btnSalvarSolicitacao: !!btnSalvarSolicitacao,
        inputSolicitacao: !!inputSolicitacao
    });

    function renderSolicitacaoCriacaoRip() {
        const existing = document.getElementById('card-solicitacao-rip');
        if (existing) existing.remove();
        
        if (!window.solicitacaoCriacaoRip) return;

        const anexos = window.solicitacaoAnexos || [];
        let anexosHtml = '';
        if (anexos.length > 0) {
            anexosHtml = anexos.map(a =>
                `<div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;padding:3px 8px;background:#fff;border-radius:4px;border:1px solid #f3f4f6;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                    <a href="${a.base64}" target="_blank" download="${a.nome}" style="color:#1e3a5f;text-decoration:underline;cursor:pointer;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${a.nome}">${a.nome}</a>
                </div>`
            ).join('');
        }

        const div = document.createElement('div');
        div.id = 'card-solicitacao-rip';
        div.style.cssText = "background-color: #fdf2f8; border: 1px solid #fbcfe8; padding: 10px 14px; border-radius: 6px; display: flex; justify-content: space-between; align-items: flex-start; font-size: 14px; font-weight: 500; color: #9d174d; margin-top: 8px; flex-direction: column; gap: 6px; text-align: left;";
        div.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span>🔔 Solicitação de Criação de RIP</span>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="cursor: pointer; color: #0284c7; font-weight: bold; font-size: 12px;" id="btnEditarSolicitacaoRip" title="Editar">Editar</span>
                    <span style="cursor: pointer; color: #ef4444; font-weight: bold; font-size: 18px;" id="btnExcluirSolicitacaoRip" title="Remover">&times;</span>
                </div>
            </div>
            <div style="font-size: 13px; color: #475569; background: #fff; padding: 6px 10px; border-radius: 4px; border: 1px solid #f3f4f6; width: 100%; box-sizing: border-box; word-break: break-all;">
                ${window.solicitacaoCriacaoRip}
            </div>
            ${anexosHtml ? `<div style="display:flex;flex-direction:column;gap:4px;width:100%;">${anexosHtml}</div>` : ''}
        `;
        const listRips = document.getElementById('listaRipsInseridos');
        if (listRips) listRips.appendChild(div);

        div.querySelector('#btnEditarSolicitacaoRip').addEventListener('click', () => {
            if (inputSolicitacao) inputSolicitacao.value = window.solicitacaoCriacaoRip;
            renderAnexosSolicitacao();
            if (modalSolicitacao) modalSolicitacao.style.display = 'flex';
        });

        div.querySelector('#btnExcluirSolicitacaoRip').addEventListener('click', () => {
            window.solicitacaoCriacaoRip = "";
            window.solicitacaoAnexos = [];
            div.remove();
            atualizarHiddenSolicitacao();
            atualizarVisibilidadeSecaoImovel();
            atualizarLayoutConceituacao();
        });
    }

    if (btnSolicitar && modalSolicitacao) {
        btnSolicitar.addEventListener('click', () => {
            console.log("🔍 [foco-01] Clique detectado em btnSolicitar! Abrindo modal...");
            modalSolicitacao.style.display = 'flex';
            if (inputSolicitacao) inputSolicitacao.value = window.solicitacaoCriacaoRip || '';
        });
    }

    const fecharModalSolicitacao = () => { if (modalSolicitacao) modalSolicitacao.style.display = 'none'; };
    if (btnFecharSolicitacao) btnFecharSolicitacao.addEventListener('click', fecharModalSolicitacao);
    if (btnCancelarSolicitacao) btnCancelarSolicitacao.addEventListener('click', fecharModalSolicitacao);

    if (btnSalvarSolicitacao) {
        btnSalvarSolicitacao.addEventListener('click', () => {
            if (!inputSolicitacao) return;
            const txt = inputSolicitacao.value.trim();
            if (!txt) {
                alert("Por favor, descreva sua solicitação ao setor de cadastro.");
                return;
            }
            window.solicitacaoCriacaoRip = txt;
            window.solicitacaoAnexos = coletarAnexosSolicitacao();
            renderSolicitacaoCriacaoRip();
            atualizarHiddenSolicitacao();
            atualizarVisibilidadeSecaoImovel();
            fecharModalSolicitacao();
            atualizarLayoutConceituacao();
        });
    }

    // Anexos da Solicitação de Criação de RIP
    window.solicitacaoAnexos = window.solicitacaoAnexos || [];

    const btnAddAnexo = document.getElementById('btnAddAnexoSolicitacao');
    const anexosContainer = document.getElementById('anexosSolicitacaoContainer');

    function renderAnexosSolicitacao() {
        if (!anexosContainer) return;
        anexosContainer.innerHTML = '';
        const anexos = window.solicitacaoAnexos || [];
        anexos.forEach((anexo, idx) => {
            const row = document.createElement('div');
            row.style.cssText = 'display: flex; gap: 8px; align-items: center; background: #f8fafc; padding: 6px 10px; border-radius: 4px; border: 1px solid #e2e8f0;';
            row.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                <a href="${anexo.base64}" target="_blank" download="${anexo.nome}" style="flex:1; font-size: 13px; color: #1e3a5f; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-decoration: underline;" title="${anexo.nome}">${anexo.nome}</a>
                <button type="button" style="background:none; border:none; cursor:pointer; color:#ef4444; font-size:16px; padding:0; line-height:1;" data-idx="${idx}" title="Remover anexo">&times;</button>
            `;
            row.querySelector('button').addEventListener('click', () => {
                window.solicitacaoAnexos.splice(idx, 1);
                renderAnexosSolicitacao();
            });
            anexosContainer.appendChild(row);
        });
    }

    function coletarAnexosSolicitacao() {
        return window.solicitacaoAnexos || [];
    }

    if (btnAddAnexo && anexosContainer) {
        const fileInputHidden = document.createElement('input');
        fileInputHidden.type = 'file';
        fileInputHidden.style.display = 'none';
        fileInputHidden.multiple = true;
        anexosContainer.parentElement.appendChild(fileInputHidden);

        btnAddAnexo.addEventListener('click', () => {
            fileInputHidden.click();
        });

        fileInputHidden.addEventListener('change', () => {
            const files = fileInputHidden.files;
            if (!files || files.length === 0) return;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    window.solicitacaoAnexos.push({
                        nome: file.name,
                        tipo: file.type,
                        base64: e.target.result
                    });
                    renderAnexosSolicitacao();
                };
                reader.readAsDataURL(file);
            }
            fileInputHidden.value = '';
        });
    }

    // Restaura anexos ao abrir o modal
    if (btnSolicitar && modalSolicitacao) {
        btnSolicitar.addEventListener('click', () => {
            renderAnexosSolicitacao();
        });
    }

    function atualizarHiddenSolicitacao() {
        const hidden = document.getElementById('hiddenSolicitacaoCriacaoRip');
        if (hidden) hidden.value = window.solicitacaoCriacaoRip || '';
    }

    function atualizarNumeroRequerimento() {
        // Não sobrescreve o número do requerimento original
    }

    window.removerRipItem = function(rip) {
        window.ripsPendentes = window.ripsPendentes.filter(r => (typeof r === 'string' ? r : r.numero_rip) !== rip);
        
        atualizarLayoutConceituacao();
        atualizarVisibilidadeSecaoImovel();
        if (window.parent && typeof window.parent.updateField === 'function') {
            window.parent.updateField('rips', window.ripsPendentes);
        }
    };

    window.removerCadastroItem = function(cep, area) {
        window.cadastrosPendentes = window.cadastrosPendentes.filter(c => c.cep !== cep || c.area !== area);
        atualizarLayoutConceituacao();
        atualizarVisibilidadeSecaoImovel();
    };

    const listaRipsInseridos = document.getElementById('listaRipsInseridos');
    const listaCadastrosInseridos = document.getElementById('listaCadastrosInseridos');
    const selectConceituacao = document.getElementById('selectConceituacao');

    function atualizarVisibilidadeSecaoImovel() {
        const temRips = window.ripsPendentes && window.ripsPendentes.length > 0;
        const temCadastros = window.cadastrosPendentes && window.cadastrosPendentes.length > 0;
        const temSolicitacao = !!window.solicitacaoCriacaoRip;
        if (listaRipsInseridos) listaRipsInseridos.style.display = (temRips || temSolicitacao) ? 'flex' : 'none';
        if (listaCadastrosInseridos) listaCadastrosInseridos.style.display = temCadastros ? 'flex' : 'none';
    }

    if (selectConceituacao) {
        selectConceituacao.addEventListener('change', () => {
            atualizarLayoutConceituacao();
        });
    }

    // Inicializa o layout para deixar o botão desabilitado caso nada esteja marcado inicialmente
    atualizarLayoutConceituacao();
    atualizarVisibilidadeSecaoImovel();

    // =========================================================================
    // 5. LÓGICA DOS MODAIS (RIP E CADASTRO MÍNIMO)
    // =========================================================================

    // Elementos do Modal Inserir RIP
    const modalRip = document.getElementById('modalInserirRip');
    const btnFecharModalRip = document.getElementById('btnFecharModalRip');
    const btnCancelarRip = document.getElementById('btnCancelarRip');
    const btnSalvarRip = document.getElementById('btnSalvarRip');
    const btnMaisRip = document.getElementById('btnMaisRip');
    const btnPesquisarRip = document.getElementById('btnPesquisarRip');
    const inputNumeroRip = document.getElementById('inputNumeroRip');
    const btnInserirRip = document.getElementById('btnInserirRip');
    
    // Arrays para manter os dados pendentes de salvamento
    window.ripsPendentes = [];
    window.cadastrosPendentes = [];

    if (btnInserirRip && modalRip) {
        btnInserirRip.addEventListener('click', () => {
            modalRip.style.display = 'flex';
            inputNumeroRip.value = '';
        });
    }

    const fecharModalRip = () => { 
        if (modalRip) modalRip.style.display = 'none'; 
        const dadosBox = document.getElementById('dadosRipPesquisado');
        if (dadosBox) dadosBox.style.display = 'none';
    };
    if (btnFecharModalRip) btnFecharModalRip.addEventListener('click', fecharModalRip);
    if (btnCancelarRip) btnCancelarRip.addEventListener('click', fecharModalRip);
    if (inputNumeroRip) inputNumeroRip.addEventListener('input', limparErroRip);
    
    function adicionarRipNaLista(ripInput, cep = '', logradouro = '', municipio = '', uf = '', destTerreno = 'Integral', areaTerreno = '', destImovel = 'Integral', areaImovel = '') {
        if (!listaRipsInseridos) return;

        let rip, destT, areaT, destI, areaI;
        if (typeof ripInput === 'object' && ripInput !== null) {
            rip = ripInput.numero_rip;
            cep = ripInput.cep || cep;
            logradouro = ripInput.logradouro || logradouro;
            municipio = ripInput.municipio || municipio;
            uf = ripInput.uf || uf;
            destT = ripInput.destinacao_terreno || 'Integral';
            areaT = ripInput.area_terreno_parcial || '';
            destI = ripInput.destinacao_imovel || 'Integral';
            areaI = ripInput.area_imovel_parcial || '';
        } else {
            rip = ripInput;
            destT = destTerreno;
            areaT = areaTerreno;
            destI = destImovel;
            areaI = areaImovel;
        }

        const div = document.createElement('div');
        div.style.cssText = "background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 4px; font-size: 14px; font-weight: 500; color: #166534; margin-bottom: 8px;";
        
        let addressText = '';
        if (cep || logradouro) {
            addressText = `<br><span style="font-weight: normal; color: #475569; font-size: 0.9em; display: block; margin-top: 4px;">📍 ${logradouro || ''} - ${municipio || ''}/${uf || ''} (CEP: ${cep || ''})</span>`;
        }

        const destinacaoTxt = (pergunta, dest, area) => {
            const complemento = dest === 'Parcial' ? ` — <strong>Metragem:</strong> ${area} m²` : '';
            return `<div style="margin-bottom: 5px;"><span style="font-weight: 600; color: #1e293b;">${pergunta}</span><br><span style="color: #166534;">${dest}</span>${complemento}</div>`;
        };

        const ripObj = {
            numero_rip: rip,
            cep: cep,
            logradouro: logradouro,
            municipio: municipio,
            uf: uf,
            destinacao_terreno: destT,
            area_terreno_parcial: areaT,
            destinacao_imovel: destI,
            area_imovel_parcial: areaI
        };
        const dadosJsonStr = JSON.stringify(ripObj).replace(/"/g, '&quot;');
        
        div.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                <div style="flex: 1;">
                    <span>✅ RIP Cadastrado: <strong>${rip}</strong></span>
                    ${addressText}
                </div>
                <span style="cursor: pointer; color: #ef4444; font-size: 20px; font-weight: bold;" onclick="this.parentElement.parentElement.remove(); window.removerRipItem('${rip}');" title="Remover">&times;</span>
            </div>
            <div style="margin-top: 8px; padding: 10px 12px; background: #ffffff; border: 1px solid #bbf7d0; border-radius: 4px; font-weight: normal; color: #334155; font-size: 0.9em;">
                ${destinacaoTxt('Qual a área do terreno a ser destinada?', destT, areaT)}
                ${destinacaoTxt('Qual a área do imóvel a ser destinada?', destI, areaI)}
            </div>
            <input type="hidden" name="rips[]" value="${dadosJsonStr}">
        `;
        listaRipsInseridos.appendChild(div);
        
        window.ripsPendentes = window.ripsPendentes.filter(r => (typeof r === 'string' ? r : r.numero_rip) !== rip);
        window.ripsPendentes.push(ripObj);

        const containerDropdown = document.getElementById('container_conceituacao_dropdown');
        if (containerDropdown) {
            containerDropdown.style.display = 'none';
        }

        atualizarLayoutConceituacao();
        atualizarVisibilidadeSecaoImovel();
    }

    // Função para validar RIP na tabela_spu
    async function validarRipNoBanco(rip) {
        try {
            const SUPA_URL = window.SUPABASE_URL || (window.parent && window.parent.SUPABASE_URL);
            const SUPA_KEY = window.SUPABASE_ANON_KEY || (window.parent && window.parent.SUPABASE_ANON_KEY);
            if (!SUPA_URL || !SUPA_KEY) return true; // fallback: aceita se não conseguir conectar
            const url = `${SUPA_URL}/rest/v1/tabela_spu?select=numero_rip&numero_rip=eq.${rip}&limit=1`;
            const res = await fetch(url, { headers: { apikey: SUPA_KEY, Authorization: `Bearer ${SUPA_KEY}` } });
            if (!res.ok) return true;
            const data = await res.json();
            return data.length > 0;
        } catch (e) {
            console.error('[foco-01] Erro ao validar RIP:', e);
            return true;
        }
    }

    function mostrarErroRip(msg) {
        const el = document.getElementById('errRipNaoEncontrado');
        if (el) { el.textContent = msg; el.style.display = 'block'; }
    }
    function limparErroRip() {
        const el = document.getElementById('errRipNaoEncontrado');
        if (el) el.style.display = 'none';
    }

    if (btnSalvarRip) {
        console.log('[foco-01] btnSalvarRip encontrado, adicionando listener');
        btnSalvarRip.addEventListener('click', async () => {
            try {
                console.log('[foco-01] btnSalvarRip clicado, inputNumeroRip:', inputNumeroRip);
                limparErroRip();
                const rip = inputNumeroRip ? inputNumeroRip.value.trim() : '';
                console.log('[foco-01] RIP digitado:', JSON.stringify(rip));
                if (rip === '') return;
                
                const spuData = await window.fetchSPU(rip);
                const existe = spuData && (spuData.numero_rip || spuData.cep);
                console.log('[foco-01] RIP existe:', existe);
                if (!existe) {
                    mostrarErroRip('RIP não encontrado na tabela_spu!');
                    inputNumeroRip.style.borderColor = '#dc2626';
                    return;
                }
                inputNumeroRip.style.borderColor = '';
                
                const destTerreno = document.querySelector('input[name="destinacao_terreno_rip"]:checked')?.value || 'Integral';
                const areaTerreno = destTerreno === 'Parcial' ? (document.getElementById('modalAreaTerrenoRip')?.value || '') : '';
                const destImovel = document.querySelector('input[name="destinacao_imovel_rip"]:checked')?.value || 'Integral';
                const areaImovel = destImovel === 'Parcial' ? (document.getElementById('modalAreaImovelRip')?.value || '') : '';

                adicionarRipNaLista(rip, spuData.cep, spuData.logradouro, spuData.municipio, spuData.uf, destTerreno, areaTerreno, destImovel, areaImovel);
                
                // Reset inputs
                document.querySelectorAll('input[name="destinacao_terreno_rip"]').forEach((r, idx) => r.checked = idx === 0);
                document.querySelectorAll('input[name="destinacao_imovel_rip"]').forEach((r, idx) => r.checked = idx === 0);
                if (document.getElementById('modalAreaTerrenoRip')) document.getElementById('modalAreaTerrenoRip').value = '';
                if (document.getElementById('modalAreaImovelRip')) document.getElementById('modalAreaImovelRip').value = '';
                if (document.getElementById('containerAreaTerrenoParcialRip')) document.getElementById('containerAreaTerrenoParcialRip').style.display = 'none';
                if (document.getElementById('containerAreaImovelParcialRip')) document.getElementById('containerAreaImovelParcialRip').style.display = 'none';

                fecharModalRip();
                if (window.parent && typeof window.parent.updateField === 'function') {
                    window.parent.updateField('rips', window.ripsPendentes);
                }
            } catch (err) {
                console.error('[foco-01] ERRO ao salvar RIP:', err);
            }
        });
    }

    if (btnPesquisarRip) {
        btnPesquisarRip.addEventListener('click', async () => {
            limparErroRip();
            const rip = inputNumeroRip ? inputNumeroRip.value.trim() : '';
            if (rip === '') return;
            
            // UI feedback
            const originalText = btnPesquisarRip.innerHTML;
            btnPesquisarRip.innerHTML = 'Pesquisando...';
            btnPesquisarRip.disabled = true;

            const spuData = await window.fetchSPU(rip);
            const existe = spuData && (spuData.numero_rip || spuData.cep);
            
            btnPesquisarRip.innerHTML = originalText;
            btnPesquisarRip.disabled = false;

            const dadosBox = document.getElementById('dadosRipPesquisado');
            if (!existe) {
                mostrarErroRip('RIP não encontrado na tabela_spu!');
                inputNumeroRip.style.borderColor = '#dc2626';
                if (dadosBox) dadosBox.style.display = 'none';
                return;
            }
            
            inputNumeroRip.style.borderColor = '#22c55e'; // Green highlight for success
            if (dadosBox) {
                dadosBox.style.display = 'block';
                document.getElementById('ripEndereco').textContent = spuData.logradouro || '-';
                document.getElementById('ripCep').textContent = spuData.cep || '-';
                document.getElementById('ripBairro').textContent = spuData.bairro || '-';
                document.getElementById('ripMunicipio').textContent = spuData.municipio || '-';
                document.getElementById('ripUf').textContent = spuData.uf || '-';
            }
        });
    }
    if (btnMaisRip) {
        btnMaisRip.addEventListener('click', async () => {
            limparErroRip();
            const rip = inputNumeroRip.value.trim();
            if (rip === '') return;
            
            const spuData = await window.fetchSPU(rip);
            const existe = spuData && (spuData.numero_rip || spuData.cep);
            if (!existe) {
                mostrarErroRip('RIP não encontrado na tabela_spu!');
                inputNumeroRip.style.borderColor = '#dc2626';
                return;
            }
            inputNumeroRip.style.borderColor = '';
            
            const destTerreno = document.querySelector('input[name="destinacao_terreno_rip"]:checked')?.value || 'Integral';
            const areaTerreno = destTerreno === 'Parcial' ? (document.getElementById('modalAreaTerrenoRip')?.value || '') : '';
            const destImovel = document.querySelector('input[name="destinacao_imovel_rip"]:checked')?.value || 'Integral';
            const areaImovel = destImovel === 'Parcial' ? (document.getElementById('modalAreaImovelRip')?.value || '') : '';

            adicionarRipNaLista(rip, spuData.cep, spuData.logradouro, spuData.municipio, spuData.uf, destTerreno, areaTerreno, destImovel, areaImovel);
            
            inputNumeroRip.value = '';
            inputNumeroRip.style.borderColor = '';
            
            // Reset inputs
            document.querySelectorAll('input[name="destinacao_terreno_rip"]').forEach((r, idx) => r.checked = idx === 0);
            document.querySelectorAll('input[name="destinacao_imovel_rip"]').forEach((r, idx) => r.checked = idx === 0);
            if (document.getElementById('modalAreaTerrenoRip')) document.getElementById('modalAreaTerrenoRip').value = '';
            if (document.getElementById('modalAreaImovelRip')) document.getElementById('modalAreaImovelRip').value = '';
            if (document.getElementById('containerAreaTerrenoParcialRip')) document.getElementById('containerAreaTerrenoParcialRip').style.display = 'none';
            if (document.getElementById('containerAreaImovelParcialRip')) document.getElementById('containerAreaImovelParcialRip').style.display = 'none';

            const dadosBox = document.getElementById('dadosRipPesquisado');
            if (dadosBox) dadosBox.style.display = 'none';
            inputNumeroRip.focus();
            if (window.parent && typeof window.parent.updateField === 'function') {
                window.parent.updateField('rips', window.ripsPendentes);
            }
        });
    }

    // Elementos do Modal Cadastro Mínimo
    const modalCadastro = document.getElementById('modalCadastroMinimo');
    const btnFecharModalCadastroMinimo = document.getElementById('btnFecharModalCadastroMinimo');
    const btnCancelarCadastro = document.getElementById('btnCancelarCadastro');
    const btnSalvarCadastro = document.getElementById('btnSalvarCadastro');
    
    // Campos
    const modalCep = document.getElementById('modalCep');
    const modalLogradouro = document.getElementById('modalLogradouro');
    const modalMunicipio = document.getElementById('modalMunicipio');
    const modalUf = document.getElementById('modalUf');
    const modalNumero = document.getElementById('modalNumero');
    const modalArea = document.getElementById('modalArea');

    const btnInserirCadastroMinimo = document.getElementById('btnInserirCadastroMinimo');
    if (btnInserirCadastroMinimo && modalCadastro) {
        btnInserirCadastroMinimo.addEventListener('click', () => {
            modalCadastro.style.display = 'flex';
            // Initializes map correctly when modal becomes visible
            setTimeout(() => {
                if(typeof initMap === 'function') initMap();
            }, 100);
        });
    }

    const fecharModalCadastro = () => { if (modalCadastro) modalCadastro.style.display = 'none'; };
    if (btnFecharModalCadastroMinimo) btnFecharModalCadastroMinimo.addEventListener('click', fecharModalCadastro);
    if (btnCancelarCadastro) btnCancelarCadastro.addEventListener('click', fecharModalCadastro);

    function adicionarCadastroNaLista(dados) {
        if (!listaCadastrosInseridos) return;
        const div = document.createElement('div');
        div.style.cssText = "background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 4px; font-size: 14px; font-weight: 500; color: #166534; margin-bottom: 8px;";
        
        const dadosJsonStr = JSON.stringify(dados).replace(/"/g, '&quot;');
        let displayExtra = '';
        if(dados.cep) displayExtra += `CEP: ${dados.cep}`;
        else if (dados.latitude && dados.longitude) displayExtra += `Geo: ${dados.latitude}, ${dados.longitude}`;
        else displayExtra += `Sem localização`;

        const destT = dados.destinacao_terreno || 'Integral';
        const areaT = dados.area_terreno_parcial || '';
        const destI = dados.destinacao_imovel || 'Integral';
        const areaI = dados.area_imovel_parcial || '';
        const destinacaoTxt = (pergunta, dest, area) => {
            const complemento = dest === 'Parcial' ? ` — <strong>Metragem:</strong> ${area} m²` : '';
            return `<div style="margin-bottom: 5px;"><span style="font-weight: 600; color: #1e293b;">${pergunta}</span><br><span style="color: #166534;">${dest}</span>${complemento}</div>`;
        };
        const mapaId = `mapa-cad-aba1-${listaCadastrosInseridos.children.length}`;
        const mapaHtml = (dados.latitude && dados.longitude) ? `<div id="${mapaId}" data-leaflet-map style="width:100%;height:200px;border:1px solid #bbf7d0;border-radius:6px;margin-top:8px;"></div>` : '';

        let enderecoText = '';
        if (dados.logradouro) {
            enderecoText = `<br><span style="font-weight: normal; color: #475569; font-size: 0.9em; display: block; margin-top: 4px;">📍 ${dados.logradouro}${dados.numero ? ', ' + dados.numero : ''}${dados.complemento ? ' - ' + dados.complemento : ''} - ${dados.municipio || ''}/${dados.uf || ''}</span>`;
        }

        div.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                <div style="flex: 1;">
                    <span>✅ Cadastro realizado <span style="font-size: 12px; color: #15803d; font-weight: normal; margin-left: 5px;">(${displayExtra})</span></span>
                    ${enderecoText}
                </div>
                <span style="cursor: pointer; color: #ef4444; font-size: 20px; font-weight: bold;" onclick="this.parentElement.parentElement.remove(); window.removerCadastroItem('${dados.cep || ''}', '${dados.area || ''}');" title="Remover">&times;</span>
            </div>
            <div style="margin-top: 8px; padding: 10px 12px; background: #ffffff; border: 1px solid #bbf7d0; border-radius: 4px; font-weight: normal; color: #334155; font-size: 0.9em;">
                ${destinacaoTxt('Qual a área do terreno a ser destinada?', destT, areaT)}
                ${destinacaoTxt('Qual a área do imóvel a ser destinada?', destI, areaI)}
            </div>
            ${mapaHtml}
            <input type="hidden" name="cadastros_minimos[]" value="${dadosJsonStr}">
        `;
        listaCadastrosInseridos.appendChild(div);
        if (dados.latitude && dados.longitude && typeof initMapCadastro === 'function') {
            initMapCadastro(mapaId, dados.latitude, dados.longitude);
        }
        
        const exists = window.cadastrosPendentes.some(c => c.cep === dados.cep && c.area === dados.area && c.latitude === dados.latitude);
        if (!exists) window.cadastrosPendentes.push(dados);

        atualizarLayoutConceituacao();
        atualizarVisibilidadeSecaoImovel();
    }

    let map = null;
    let marker = null;
    function initMap() {
        if (!map && document.getElementById('mapaCadastro')) {
            map = L.map('mapaCadastro').setView([-15.7938, -47.8827], 4);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
            map.on('click', function(e) {
                if(marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('modalLatitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('modalLongitude').value = e.latlng.lng.toFixed(6);
            });
        }
        if (map) {
            setTimeout(() => map.invalidateSize(), 200);
        }
    }

    // Toggle layout between CEP and Coordenadas
    const radiosModo = document.querySelectorAll('input[name="modo_localizacao"]');
    const blocoCep = document.getElementById('blocoEntradaCep');
    const blocoCoord = document.getElementById('blocoEntradaCoord');
    const helpText = document.getElementById('mapaHelpText');
    
    radiosModo.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if(e.target.value === 'CEP') {
                blocoCep.style.display = 'block';
                blocoCoord.style.display = 'none';
                if(helpText) helpText.textContent = "Digite o CEP para aproximar o mapa e, em seguida, clique no local exato do imóvel.";
            } else {
                blocoCep.style.display = 'none';
                blocoCoord.style.display = 'grid';
                if(helpText) helpText.textContent = "Digite as coordenadas ou clique diretamente no mapa para marcar o local exato do imóvel.";
            }
        });
    });

    // Handle map search via CEP Button
    const btnLocalizarCep = document.getElementById('btnLocalizarCep');
    if (btnLocalizarCep && modalCep) {
        btnLocalizarCep.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const cepVal = modalCep.value.replace(/\D/g, '');
            if(cepVal.length !== 8) {
                alert('Digite um CEP válido (8 dígitos).');
                return;
            }
            
            if(!map) {
                alert('O mapa não foi carregado corretamente. Tentando recarregar...');
                if(typeof initMap === 'function') initMap();
                return;
            }

            const originalText = btnLocalizarCep.innerHTML;
            btnLocalizarCep.innerHTML = 'Buscando...';
            btnLocalizarCep.disabled = true;
            try {
                // Passo 1: Busca o endereço exato no ViaCEP
                const viacepRes = await fetch(`https://viacep.com.br/ws/${cepVal}/json/`);
                const viacepData = await viacepRes.json();
                
                if (!viacepData.erro) {
                    // Preenche os campos do formulário automaticamente
                    if (document.getElementById('modalLogradouro')) document.getElementById('modalLogradouro').value = viacepData.logradouro || '';
                    if (document.getElementById('modalMunicipio')) document.getElementById('modalMunicipio').value = viacepData.localidade || '';
                    if (document.getElementById('modalUf')) document.getElementById('modalUf').value = viacepData.uf || '';
                        
                        // Passo 2: Busca as coordenadas no Nominatim usando o endereço completo
                        const query = `${viacepData.logradouro ? viacepData.logradouro + ',' : ''} ${viacepData.localidade}, ${viacepData.uf}, Brazil`;
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json`);
                        const data = await res.json();
                        
                        if(data && data.length > 0) {
                            const lat = data[0].lat;
                            const lon = data[0].lon;
                            map.setView([lat, lon], 16);
                            if(marker) map.removeLayer(marker);
                            marker = L.marker([lat, lon]).addTo(map);
                            document.getElementById('modalLatitude').value = lat;
                            document.getElementById('modalLongitude').value = lon;
                        } else {
                            // Tenta buscar apenas pela cidade se a rua falhar
                            const queryCity = `${viacepData.localidade}, ${viacepData.uf}, Brazil`;
                            const resCity = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(queryCity)}&format=json`);
                            const dataCity = await resCity.json();
                            if(dataCity && dataCity.length > 0) {
                                const lat = dataCity[0].lat;
                                const lon = dataCity[0].lon;
                                map.setView([lat, lon], 12);
                                if(marker) map.removeLayer(marker);
                                marker = L.marker([lat, lon]).addTo(map);
                                document.getElementById('modalLatitude').value = lat;
                                document.getElementById('modalLongitude').value = lon;
                                alert('O mapa foi centralizado na cidade, pois não achamos as coordenadas exatas da rua. Clique no local exato.');
                            } else {
                                alert('Endereço encontrado, mas não conseguimos localizar no mapa. Por favor, marque manualmente.');
                            }
                        }
                    } else {
                        alert('CEP não encontrado na base de dados (ViaCEP).');
                    }
                } catch(err) { 
                    console.error('ViaCEP/Nominatim search failed', err); 
                    alert('Erro ao buscar o CEP na internet.');
                }
                btnLocalizarCep.innerHTML = originalText;
                btnLocalizarCep.disabled = false;
        });
    }

    // Handle map search via Coordenadas Button
    const btnLocalizarCoord = document.getElementById('btnLocalizarCoord');
    if (btnLocalizarCoord) {
        btnLocalizarCoord.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if(!map) {
                alert('O mapa não foi carregado corretamente. Tentando recarregar...');
                if(typeof initMap === 'function') initMap();
                return;
            }

            const latInput = document.getElementById('modalLatitude');
            const lngInput = document.getElementById('modalLongitude');
            if(latInput && lngInput && latInput.value && lngInput.value) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.setView([lat, lng], 16);
                    if(marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map);
                } else {
                    alert('Coordenadas inválidas. Digite números válidos.');
                }
            } else {
                alert('Preencha Latitude e Longitude.');
            }
        });
    }

    // Toggle destinação área
    document.querySelectorAll('input[name="destinacao_terreno"]').forEach(r => {
        r.addEventListener('change', e => document.getElementById('containerAreaTerrenoParcial').style.display = e.target.value === 'Parcial' ? 'flex' : 'none');
    });
    document.querySelectorAll('input[name="destinacao_imovel"]').forEach(r => {
        r.addEventListener('change', e => document.getElementById('containerAreaImovelParcial').style.display = e.target.value === 'Parcial' ? 'flex' : 'none');
    });
    document.querySelectorAll('input[name="destinacao_terreno_rip"]').forEach(r => {
        r.addEventListener('change', e => document.getElementById('containerAreaTerrenoParcialRip').style.display = e.target.value === 'Parcial' ? 'flex' : 'none');
    });
    document.querySelectorAll('input[name="destinacao_imovel_rip"]').forEach(r => {
        r.addEventListener('change', e => document.getElementById('containerAreaImovelParcialRip').style.display = e.target.value === 'Parcial' ? 'flex' : 'none');
    });

    if (btnSalvarCadastro) {
        btnSalvarCadastro.addEventListener('click', () => {
            const cep = modalCep ? modalCep.value.trim() : '';
            const modo = document.querySelector('input[name="modo_localizacao"]:checked')?.value || 'CEP';
            const latitude = document.getElementById('modalLatitude')?.value.trim() || '';
            const longitude = document.getElementById('modalLongitude')?.value.trim() || '';
            
            const destTerreno = document.querySelector('input[name="destinacao_terreno"]:checked')?.value || 'Integral';
            const areaTerreno = destTerreno === 'Parcial' ? (document.getElementById('modalAreaTerreno')?.value || '') : '';
            
            const destImovel = document.querySelector('input[name="destinacao_imovel"]:checked')?.value || 'Integral';
            const areaImovel = destImovel === 'Parcial' ? (document.getElementById('modalAreaImovel')?.value || '') : '';

            const area = areaTerreno || areaImovel || '0';

            const dados = {
                cep: cep,
                logradouro: modalLogradouro ? modalLogradouro.value : '',
                municipio: modalMunicipio ? modalMunicipio.value : '',
                uf: modalUf ? modalUf.value : '',
                numero: modalNumero ? modalNumero.value : '',
                complemento: typeof modalComplemento !== 'undefined' && modalComplemento ? modalComplemento.value : '',
                area: area,
                observacoes: document.getElementById('modalObservacoes') ? document.getElementById('modalObservacoes').value : '',
                modo_localizacao: modo,
                latitude: latitude,
                longitude: longitude,
                destinacao_terreno: destTerreno,
                area_terreno_parcial: areaTerreno,
                destinacao_imovel: destImovel,
                area_imovel_parcial: areaImovel
            };

            adicionarCadastroNaLista(dados);
            fecharModalCadastro();
            
            // Limpa form basico
            if (modalCep) modalCep.value = '';
            if (document.getElementById('modalAreaTerreno')) document.getElementById('modalAreaTerreno').value = '';
            if (document.getElementById('modalAreaImovel')) document.getElementById('modalAreaImovel').value = '';
            if (modalLogradouro) modalLogradouro.value = '';
            if (modalMunicipio) modalMunicipio.value = '';
            if (modalUf) modalUf.value = '';
            if (modalNumero) modalNumero.value = '';
            if (document.getElementById('modalLatitude')) document.getElementById('modalLatitude').value = '';
            if (document.getElementById('modalLongitude')) document.getElementById('modalLongitude').value = '';
            if (document.getElementById('modalAreaTerreno')) document.getElementById('modalAreaTerreno').value = '';
            if (document.getElementById('modalAreaImovel')) document.getElementById('modalAreaImovel').value = '';
            if (document.getElementById('modalObservacoes')) document.getElementById('modalObservacoes').value = '';

            if (window.parent && typeof window.parent.updateField === 'function') {
                window.parent.updateField('cadastros_minimos', window.cadastrosPendentes);
            }
        });
    }

    // Carregar dados iniciais, se existirem
    setTimeout(async () => {
        const processId = localStorage.getItem('CURRENT_PROCESS_ID');
        const fnInd = window.carregarIndicacoes || (window.parent && window.parent.carregarIndicacoes);
        let registro = null;
        if (processId && typeof fnInd === 'function') {
            try {
                registro = await fnInd(processId);
            } catch(e) {
                console.warn('Erro ao carregar indicações via Supabase:', e);
            }
        }

        // Dados inline do Laravel (draft) são a fonte primária
        let rips = (window.INLINE_RIPS && window.INLINE_RIPS.length > 0) ? window.INLINE_RIPS : [];
        let cadastros = (window.INLINE_CADASTROS && window.INLINE_CADASTROS.length > 0) ? window.INLINE_CADASTROS : [];

        // Fallback para Supabase se não houver dados inline
        if (rips.length === 0 && registro && registro.dados_json && registro.dados_json.rips) {
            rips = registro.dados_json.rips;
        }
        if (cadastros.length === 0 && registro && registro.dados_json && registro.dados_json.cadastros_minimos) {
            cadastros = registro.dados_json.cadastros_minimos;
        }

        // Solicitação de RIP: prefere dados inline (Laravel draft), fallback Supabase
        if (window.INLINE_SOLICITACAO_RIP) {
            window.solicitacaoCriacaoRip = window.INLINE_SOLICITACAO_RIP;
        } else if (registro && registro.dados_json && registro.dados_json.solicitacao_criacao_rip) {
            window.solicitacaoCriacaoRip = registro.dados_json.solicitacao_criacao_rip;
        }

        if (window.INLINE_SOLICITACAO_ANEXOS && window.INLINE_SOLICITACAO_ANEXOS.length > 0) {
            window.solicitacaoAnexos = window.INLINE_SOLICITACAO_ANEXOS;
        }
        atualizarHiddenSolicitacao();

        if (rips.length > 0 || cadastros.length > 0 || window.solicitacaoCriacaoRip || (registro && registro.dados_json)) {
            for (const ripData of rips) {
                const ripObj = typeof ripData === 'string' ? { numero_rip: ripData } : ripData;
                const rip = ripObj.numero_rip;
                try {
                    const spuData = await window.fetchSPU(rip);
                    const fullRipObj = {
                        ...ripObj,
                        cep: ripObj.cep || spuData.cep || '',
                        logradouro: ripObj.logradouro || spuData.logradouro || '',
                        municipio: ripObj.municipio || spuData.municipio || '',
                        uf: ripObj.uf || spuData.uf || ''
                    };
                    adicionarRipNaLista(fullRipObj);
                } catch (e) {
                    console.warn('[foco-01] Erro ao buscar dados do RIP ' + rip + ', adicionando sem endereço:', e);
                    adicionarRipNaLista(ripObj);
                }
            }
            cadastros.forEach(cad => adicionarCadastroNaLista(cad));
            if (window.solicitacaoCriacaoRip) {
                renderSolicitacaoCriacaoRip();
            }
            atualizarVisibilidadeSecaoImovel();

            // Restaura a conceituação do imóvel
            const selectCon = document.getElementById('conceituacao_imovel');
            if (selectCon && registro && registro.dados_json) {
                let val = registro.dados_json.conceituacao_imovel || '';
                if (val) {
                    selectCon.value = val;
                }
            }
            atualizarLayoutConceituacao();
        } else {
            atualizarLayoutConceituacao();
        }
    }, 500); // Aguarda o db.js estar pronto e a tela carregar

    // Lógica para Salvar e Manifestação
    const formReq = document.getElementById('form01');
    let ultimoRelatorioSalvo = {};

    async function executarSalvamento() {
        if (!formReq.checkValidity()) {
            formReq.reportValidity();
            return false;
        }
        
        const processId = localStorage.getItem('CURRENT_PROCESS_ID');
        if (window.parent) {
            const selectCon = document.getElementById('conceituacao_imovel');
            const val = selectCon ? selectCon.value : '';
            
            const exigeRip = ["Terreno/acrescido de marinha", "Terreno/acrescido marginal", "Nacional interior"];
            const exigeCadastro = ["Espelho d'água", "Cavidades naturais subterrâneas", "Manguezal", "Praias"];
            const possuiRipVal = exigeRip.includes(val) ? 'Sim' : (exigeCadastro.includes(val) ? 'Não' : '');
            
            const cbRipsAtivos = exigeRip.includes(val) ? [val] : [];
            const cbDispensadosAtivos = exigeCadastro.includes(val) ? [val] : [];
            
            try {
                // 1. Salva na Tabela Indicação
                const dadosIndicacao = {
                    rips: window.ripsPendentes,
                    cadastros_minimos: window.cadastrosPendentes,
                    possui_rip: possuiRipVal,
                    conceituacao_imovel: val,
                    conceituacao_rip: cbRipsAtivos,
                    conceituacao_dispensado: cbDispensadosAtivos,
                    solicitacao_criacao_rip: window.solicitacaoCriacaoRip || ""
                };
                
                const url = `${window.parent.SUPABASE_URL}/rest/v1/tabela_indicacao?on_conflict=numero_requerimento`;
                const respIndicacao = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'apikey': window.parent.SUPABASE_ANON_KEY,
                        'Authorization': `Bearer ${window.parent.SUPABASE_ANON_KEY}`,
                        'Content-Type': 'application/json',
                        'Prefer': 'resolution=merge-duplicates'
                    },
                    body: JSON.stringify({ numero_requerimento: processId, dados_json: dadosIndicacao })
                });
                
                // 2. Sincroniza dados com o formDataState do parent
                if (typeof window.parent.updateField === 'function') {
                    window.parent.updateField('rips', window.ripsPendentes);
                    window.parent.updateField('solicitacao_criacao_rip', window.solicitacaoCriacaoRip || "");
                    window.parent.updateField('cadastros_minimos', window.cadastrosPendentes || []);
                }

                // 2.5 Monta e salva o Relatório (Snapshot) da Aba 1
                try {
                    ultimoRelatorioSalvo = {
                        numero_requerimento: document.getElementById('campo11')?.value || '',
                        data_requerimento: document.getElementById('campo12')?.value || '',
                        processo_sei: document.getElementById('campo13')?.value || '',
                        cpf_cnpj: document.getElementById('campo14')?.value || '',
                        nome_requerente: document.getElementById('campo15')?.value || '',
                        telefone: document.getElementById('campo19')?.value || '',
                        cpf_cnpj_rep: document.getElementById('campo14_rep')?.value || '',
                        nome_rep: document.getElementById('campo15_rep')?.value || '',
                        telefone_rep: document.getElementById('campo19_rep')?.value || '',
                        pessoa_estrangeira: document.getElementById('campo17')?.value || '',
                        prioridade_legal: document.getElementById('prioridade_legal')?.value || '',
                        conceituacao_imovel: val,
                        rips: window.ripsPendentes || [],
                        cadastros_minimos: window.cadastrosPendentes || [],
                        solicitacao_criacao_rip: window.solicitacaoCriacaoRip || ""
                    };

                    const urlRel = `${window.parent.SUPABASE_URL}/rest/v1/tabela_relatorios?on_conflict=process_id,aba`;
                    const payloadRel = {
                        process_id: processId,
                        aba: 'aba1',
                        dados_relatorio: ultimoRelatorioSalvo,
                        updated_at: new Date().toISOString()
                    };

                    await fetch(urlRel, {
                        method: 'POST',
                        headers: {
                            'apikey': window.parent.SUPABASE_ANON_KEY,
                            'Authorization': `Bearer ${window.parent.SUPABASE_ANON_KEY}`,
                            'Content-Type': 'application/json',
                            'Prefer': 'resolution=merge-duplicates'
                        },
                        body: JSON.stringify(payloadRel)
                    });

                    // --- VERSIONAMENTO: gravar snapshot na tabela_versoes_formulario ---
                    try {
                        const urlVersoes = `${window.parent.SUPABASE_URL}/rest/v1/tabela_versoes_formulario`;
                        const urlUltimaVersao = `${urlVersoes}?processo_id=eq.${encodeURIComponent(processId)}&aba=eq.aba1&order=versao.desc&limit=1`;
                        const resUltima = await fetch(urlUltimaVersao, {
                            headers: { apikey: window.parent.SUPABASE_ANON_KEY, Authorization: `Bearer ${window.parent.SUPABASE_ANON_KEY}` }
                        });
                        const arrUltima = await resUltima.json();
                        const proximaVersao = (arrUltima.length > 0 ? arrUltima[0].versao : 0) + 1;

                        await fetch(urlVersoes, {
                            method: 'POST',
                            headers: {
                                apikey: window.parent.SUPABASE_ANON_KEY,
                                Authorization: `Bearer ${window.parent.SUPABASE_ANON_KEY}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                processo_id: processId,
                                aba: 'aba1',
                                versao: proximaVersao,
                                dados_json: ultimoRelatorioSalvo,
                                criado_por: localStorage.getItem('CURRENT_USER_PROFILE') || 'SISTEMA'
                            })
                        });
                        console.log(`✅ [foco-01] Versão ${proximaVersao} gravada em tabela_versoes_formulario`);
                    } catch (errVersao) {
                        console.warn('⚠️ [foco-01] Erro ao gravar versão (não bloqueia):', errVersao);
                    }
                } catch(errRel) {
                    console.error('❌ [foco-01] Erro ao salvar relatório:', errRel);
                }
                
                // 3. Persiste formDataState na tabela_foco
                if (typeof window.parent.forceSaveDraft === 'function') {
                    await window.parent.forceSaveDraft();
                }
                
                return true;
            } catch(err) {
                console.error("❌ [foco-01] Erro durante o salvamento:", err);
                return false;
            }
        }
        return false;
    }

    if (typeof window._saveDraft !== 'function') {
        window._saveDraft = executarSalvamento;
    }

    const btnSalvarRelatorio = document.getElementById('btnSalvarRelatorio');
    if (btnSalvarRelatorio) {
        btnSalvarRelatorio.addEventListener('click', async () => {
            const orig = btnSalvarRelatorio.innerHTML;
            btnSalvarRelatorio.innerHTML = 'Salvando...';
            const sucesso = await executarSalvamento();
            btnSalvarRelatorio.innerHTML = orig;
            if (sucesso) {
                alert('Dados salvos com sucesso!');
            }
        });
    }

    // =========================================================================
    // 6. FLUXO SEQUENCIAL DE BOTÕES DO RODAPÉ
    // =========================================================================

    const btnSalvarFormulario = document.getElementById('btnSalvarFormulario');
    const btnRegistrarManifestacao = document.getElementById('btnRegistrarManifestacao');
    const btnEnviarCaracterizacao = document.getElementById('btnEnviarCaracterizacao');

    if (btnSalvarFormulario) {
        btnSalvarFormulario.addEventListener('click', async () => {
            const orig = btnSalvarFormulario.innerHTML;
            btnSalvarFormulario.innerHTML = 'Salvando...';
            const sucesso = await executarSalvamento();
            btnSalvarFormulario.innerHTML = orig;
            
            if (!sucesso) return; // Erro de validação
            
            alert('Formulário salvo com sucesso!');
            // Exibe o próximo botão
            if (btnRegistrarManifestacao) {
                btnRegistrarManifestacao.style.display = 'block';
            }
        });
    }

    if (btnRegistrarManifestacao) {
        btnRegistrarManifestacao.addEventListener('click', async () => {
            const modalAprovacao = document.getElementById('modalAprovacaoAba1');
            const chkAprovar = document.getElementById('chkAprovarAba1');
            const btnConfirmarAprov = document.getElementById('btnConfirmarAprovacao');
            const btnCancelarAprov = document.getElementById('btnCancelarAprovacao');
            const btnFecharAprov = document.getElementById('btnFecharModalAprovacao');
            const loadingRelatorio = document.getElementById('loadingRelatorio');
            const conteudoRel = document.getElementById('conteudoRelatorioAprovacao');

            if (modalAprovacao) {
                loadingRelatorio.style.display = 'block';
                conteudoRel.style.display = 'none';
                chkAprovar.checked = false;
                btnConfirmarAprov.disabled = true;
                
                modalAprovacao.style.display = 'flex';

                // Listener para ativar o botão de confirmação ao aceitar a declaração
                const onCheckChange = (ev) => {
                    btnConfirmarAprov.disabled = !ev.target.checked;
                };
                chkAprovar.removeEventListener('change', onCheckChange);
                chkAprovar.addEventListener('change', onCheckChange);

                const fecharModal = () => { modalAprovacao.style.display = 'none'; };
                if (btnCancelarAprov) btnCancelarAprov.onclick = fecharModal;
                if (btnFecharAprov) btnFecharAprov.onclick = fecharModal;

                // Carrega e renderiza os dados dinamicamente (Sem iframe)
                try {
                    const processId = localStorage.getItem('CURRENT_PROCESS_ID');
                    const SUPA_URL = window.parent?.SUPABASE_URL;
                    const SUPA_KEY = window.parent?.SUPABASE_ANON_KEY;

                    if (!SUPA_URL || !SUPA_KEY) {
                        throw new Error("Credenciais do Supabase não encontradas.");
                    }

                    const url = `${SUPA_URL}/rest/v1/tabela_relatorios?select=*&process_id=eq.${encodeURIComponent(processId)}&aba=eq.aba1&limit=1`;
                    const res = await fetch(url, {
                        headers: { 'apikey': SUPA_KEY, 'Authorization': `Bearer ${SUPA_KEY}` }
                    });

                    if (!res.ok) {
                        const errorText = await res.text();
                        throw new Error(`Status ${res.status}: ${res.statusText} - ${errorText}`);
                    }
                    
                    const data = await res.json();
                    if (data && data.length > 0) {
                        ultimoRelatorioSalvo = data[0].dados_relatorio;
                        const rel = ultimoRelatorioSalvo;

                        let cadastrosHtml = '-';
                        if (rel.cadastros_minimos && rel.cadastros_minimos.length > 0) {
                            cadastrosHtml = rel.cadastros_minimos.map(c => `<div><strong>CEP:</strong> ${c.cep}, <strong>Área:</strong> ${c.area}m²</div>`).join('');
                        }

                        let ripsHtml = (rel.rips && rel.rips.length > 0) ? rel.rips.join(', ') : '-';
                        
                        let historicoDevolucaoHtml = '';
                        if (rel.motivo_devolucao || rel.resposta_devolucao) {
                            historicoDevolucaoHtml = `
                                <div style="margin-top: 25px; padding: 20px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
                                    <h4 style="margin:0 0 15px 0; color: #d97706; border-bottom: 1px solid #fcd34d; padding-bottom: 8px;">
                                        🔄 Histórico de Devolução e Retificação
                                    </h4>
                                    <div style="margin-bottom: 15px; border-left: 4px solid #ef4444; padding-left: 12px;">
                                        <p style="margin: 0 0 5px 0; color: #b91c1c; font-size: 13px; font-weight: bold;">Motivo da Devolução (Questionamento):</p>
                                        <p style="margin: 0; font-size: 14px; color: #450a0a; white-space: pre-wrap;">${rel.motivo_devolucao || 'Não registrado'}</p>
                                    </div>
                                    <div style="border-left: 4px solid #10b981; padding-left: 12px;">
                                        <p style="margin: 0 0 5px 0; color: #047857; font-size: 13px; font-weight: bold;">Resposta da Equipe (Correção realizada):</p>
                                        <p style="margin: 0; font-size: 14px; color: #064e3b; white-space: pre-wrap;">${rel.resposta_devolucao || 'Não registrado'}</p>
                                    </div>
                                </div>
                            `;
                        }

                        let conclusaoHtml = '';
                        if (rel.aprovacao && rel.aprovacao.status) {
                            const aprovDate = new Date(rel.aprovacao.data).toLocaleString('pt-BR');
                            const perfilAss = rel.aprovacao.perfil || 'Perfil Atual';
                            const obsAss = rel.aprovacao.observacoes ? rel.aprovacao.observacoes : 'Sem observações adicionais.';
                            conclusaoHtml = `
                                <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #ccc; color: #333;">
                                    <h4 style="margin:0 0 10px 0; color: #1e3a5f;">Conclusão e Manifestação</h4>
                                    <div style="background: #e8f5e9; padding: 15px; border-radius: 4px; border-left: 4px solid #166534; margin-bottom: 15px;">
                                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #166534;">
                                            <strong>Declaração:</strong> Declaro que as informações consignadas neste formulário foram inseridas com base nos dados disponíveis nos sistemas oficiais, nos documentos constantes do processo e nas verificações realizadas no âmbito desta unidade, estando compatíveis com os elementos analisados.
                                        </p>
                                        <div style="font-size: 14px; color: #166534;">
                                            <strong>✅ Aprovado e Assinado Digitalmente</strong>
                                            <br>Data: ${aprovDate}
                                            <br>Perfil Responsável: <strong>${perfilAss}</strong>
                                        </div>
                                    </div>
                                    <div style="background: #f8fafc; padding: 15px; border-radius: 4px; border: 1px solid #cbd5e1;">
                                        <h5 style="margin:0 0 8px 0; color: #334155; font-size: 14px;">Observações da Manifestação:</h5>
                                        <p style="margin: 0; font-size: 13px; color: #475569; white-space: pre-wrap;">${obsAss}</p>
                                    </div>
                                </div>
                            `;
                        }

                        const dateObj = new Date(data[0].updated_at);
                        const dataRelatorioStr = dateObj.toLocaleString('pt-BR');

                        const html = `
                            <div class="report-section">
                                <div class="report-section-title">Informações do Requerente</div>
                                <div class="report-grid">
                                    <div class="report-item">
                                        <span class="report-label">Nome do Requerente</span>
                                        <span class="report-value">${rel.nome_requerente || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">CPF/CNPJ</span>
                                        <span class="report-value">${rel.cpf_cnpj || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Telefone</span>
                                        <span class="report-value">${rel.telefone || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Pessoa Estrangeira</span>
                                        <span class="report-value">${rel.pessoa_estrangeira || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="report-section">
                                <div class="report-section-title">Dados do Processo SEI e Requerimento</div>
                                <div class="report-grid">
                                    <div class="report-item">
                                        <span class="report-label">Número do Requerimento</span>
                                        <span class="report-value">${rel.numero_requerimento || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Data do Requerimento</span>
                                        <span class="report-value">${rel.data_requerimento || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Número do Processo SEI</span>
                                        <span class="report-value">${rel.processo_sei || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Prioridade Legal</span>
                                        <span class="report-value">${rel.prioridade_legal || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="report-section">
                                <div class="report-section-title">Dados do Representante Legal</div>
                                <div class="report-grid">
                                    <div class="report-item">
                                        <span class="report-label">Nome</span>
                                        <span class="report-value">${rel.nome_rep || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">CPF/CNPJ</span>
                                        <span class="report-value">${rel.cpf_cnpj_rep || '-'}</span>
                                    </div>
                                    <div class="report-item">
                                        <span class="report-label">Telefone</span>
                                        <span class="report-value">${rel.telefone_rep || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="report-section">
                                <div class="report-section-title">Indicação do Imóvel</div>
                                <div class="report-grid">
                                    <div class="report-item" style="grid-column: span 2;">
                                        <span class="report-label">Imóvel/Área Relacionados</span>
                                        <span class="report-value">${rel.conceituacao_imovel || '-'}</span>
                                    </div>
                                    <div class="report-item" style="grid-column: span 2;">
                                        <span class="report-label">RIPs Associados</span>
                                        <span class="report-value">${ripsHtml}</span>
                                    </div>
                                    <div class="report-item" style="grid-column: span 2;">
                                        <span class="report-label">Cadastros Mínimos</span>
                                        <div class="report-value">${cadastrosHtml}</div>
                                    </div>
                                    <div class="report-item" style="grid-column: span 2;">
                                        <span class="report-label">Solicitação de Criação de RIP / Observações</span>
                                        <span class="report-value">${rel.solicitacao_criacao_rip || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 40px; padding: 15px; border: 1px solid #ddd; border-left: 6px solid #1a7a4a; background: #fdfdfd; width: 100%; box-sizing: border-box; font-size: 0.85rem; color: #444; font-family: sans-serif;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                    <div style="line-height: 1.5;">
                                        Responsável: <span style="color:#1a4e8a; font-weight:bold;">Sistema (Geração Automática)</span><br>
                                        Status do Documento: <span style="color:#1a7a4a; font-weight:bold;">CONSOLIDADO E CONFERIDO</span>
                                    </div>
                                    <div style="text-align: right; line-height: 1.5;">
                                        <strong>Data/Hora Relatório:</strong> <span>${dataRelatorioStr}</span><br>
                                        <strong>Processo Base:</strong> <span style="font-family: monospace; color: #666;">${processId}</span>
                                    </div>
                                </div>
                                ${historicoDevolucaoHtml}
                                ${conclusaoHtml}
                            </div>
                        `;

                        conteudoRel.innerHTML = html;
                        loadingRelatorio.style.display = 'none';
                        conteudoRel.style.display = 'block';
                    } else {
                        loadingRelatorio.innerText = "Nenhum relatório salvo encontrado para este processo. Salve a Aba 1 para gerar.";
                    }
                } catch (err) {
                    console.error(err);
                    loadingRelatorio.innerText = "Erro ao carregar o relatório: " + err.message;
                }

                btnConfirmarAprov.onclick = async () => {
                    const origBtn = btnConfirmarAprov.innerHTML;
                    btnConfirmarAprov.innerHTML = 'Registrando...';
                    
                    try {
                        const processId = localStorage.getItem('CURRENT_PROCESS_ID');
                        const urlRelPatch = `${window.parent.SUPABASE_URL}/rest/v1/tabela_relatorios?process_id=eq.${encodeURIComponent(processId)}&aba=eq.aba1`;
                        
                        const observacoes = document.getElementById('txtObservacoesAba1') ? document.getElementById('txtObservacoesAba1').value : '';
                        const perfilLogado = localStorage.getItem('CURRENT_USER_PROFILE') || 'Equipe SPU/UF (Caracterização)';
                        
                        ultimoRelatorioSalvo.aprovacao = {
                            status: true,
                            data: new Date().toISOString(),
                            perfil: perfilLogado,
                            observacoes: observacoes
                        };

                        await fetch(urlRelPatch, {
                            method: 'PATCH',
                            headers: {
                                'apikey': window.parent.SUPABASE_ANON_KEY,
                                'Authorization': `Bearer ${window.parent.SUPABASE_ANON_KEY}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ dados_relatorio: ultimoRelatorioSalvo, updated_at: new Date().toISOString() })
                        });
                        
                        alert('Manifestação registrada com sucesso!');
                    } catch (e) {
                        console.error('❌ [foco-01] Erro ao salvar aprovação:', e);
                        alert('Houve um erro ao registrar a manifestação.');
                    }
                    
                    btnConfirmarAprov.innerHTML = origBtn;
                    fecharModal();
                    
                    // Exibe o terceiro botão
                    if (btnEnviarCaracterizacao) {
                        btnEnviarCaracterizacao.style.display = 'block';
                    }
                };
            }
        });
    }

    if (btnEnviarCaracterizacao) {
        btnEnviarCaracterizacao.addEventListener('click', async () => {
            // Salvar Snapshot Histórico no Supabase ANTES de limpar status_devolucao
            // (Assim a foto sai com a flag de que foi uma devolução e contém a resposta)
            if (window.parent && typeof window.parent.salvarSnapshotHistorico === 'function') {
                await window.parent.salvarSnapshotHistorico('Aba 1 (Indicação)');
            }

            // Limpeza do status_devolucao da Aba 1 (Pois o processo vai pra Aba 2)
            if (window.parent && window.parent.formDataState && window.parent.formDataState.status_devolucao === 'Aba 1') {
                delete window.parent.formDataState.status_devolucao;
                if (typeof window.parent.forceSaveDraft === 'function') {
                    await window.parent.forceSaveDraft();
                }
            }

            // Atualiza status do fluxo ao enviar
            const processId = localStorage.getItem('CURRENT_PROCESS_ID');
            if (processId && window.parent && typeof window.parent.updateStatusFluxo === 'function') {
                await window.parent.updateStatusFluxo(processId, 3); // ID 3: Diagnóstico de Imóvel (Caracterização)
            }

            const rootWindow = window.parent?.parent || window.parent || window;
            const btnTabNext = rootWindow.document?.querySelector('button[data-url="foco-02.html"]');
            if (btnTabNext) {
                btnTabNext.click();
            } else {
                console.error("Botão de Aba 2 não encontrado no Parent.");
            }
        });
    }

    // Máscara e Busca de CEP
    if (modalCep) {
        modalCep.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value.substring(0, 9);
        });
    }

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarFoco01);
} else {
    inicializarFoco01();
}
