let currentFilter = 'all';

// --- MOTOR DE CONSULTORIA ARQUITETURAL ---
function handleConsultation(e) {
    e.preventDefault();
    const prompt = document.getElementById('consultPrompt').value.toLowerCase().trim();
    const resultDiv = document.getElementById('consultationResult');
    const cardDiv = document.getElementById('recommendedPatternCard');

    if (!prompt) return;

    resultDiv.classList.remove('hidden');

    // 1. Verificação por Preset de Domínio (ex: "sistema de compras", "telemetria iot")
    const matchedDomain = domainPresets.find(domain => 
        domain.keywords.some(kw => prompt.includes(kw))
    );

    if (matchedDomain) {
        const matchedPatternsList = patterns.filter(p => matchedDomain.recommendedPatterns.includes(p.id));

        cardDiv.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-sgb-border pb-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sgb-orange block">Arquitetura de Domínio Identificada</span>
                        <h3 class="font-black text-white text-lg flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-sgb-cyan"></i> ${matchedDomain.title}
                        </h3>
                    </div>
                    <span class="bg-emerald-500/10 text-emerald-400 text-xs font-bold px-3 py-1 rounded-full border border-emerald-500/30">
                        ${matchedPatternsList.length} System Patterns Combinados
                    </span>
                </div>

                <p class="text-xs text-slate-300 leading-relaxed border-l-2 border-sgb-orange pl-3">
                    ${matchedDomain.explanation}
                </p>

                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-400 block mb-2">Padrões AWS Recomendados para este Caso:</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        ${matchedPatternsList.map(p => `
                            <div onclick="openPatternModal('${p.id}')" class="bg-sgb-surface border border-sgb-border hover:border-sgb-cyan p-3 rounded-lg cursor-pointer transition-all flex items-center justify-between group">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded bg-sgb-dark flex items-center justify-center text-sgb-cyan group-hover:text-sgb-orange">
                                        <i class="fa-solid ${p.icon}"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-white">${p.name}</h4>
                                        <span class="text-[9px] text-slate-400">Ver Solução</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-sgb-cyan"></i>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        return;
    }

    // 2. Busca Individual por Palavras-Chave nos Padrões
    let bestMatch = null;
    let maxScore = 0;

    patterns.forEach(p => {
        let score = 0;
        p.keywords.forEach(kw => {
            if (prompt.includes(kw.toLowerCase())) score += 2;
        });
        if (score > maxScore) {
            maxScore = score;
            bestMatch = p;
        }
    });

    if (maxScore > 0 && bestMatch) {
        const catName = categories.find(c => c.id === bestMatch.category).name;
        cardDiv.innerHTML = `
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-sgb-orange mb-1 block">${catName}</span>
                    <h3 class="font-black text-white text-xl flex items-center gap-2">
                        <i class="fa-solid ${bestMatch.icon} text-sgb-cyan"></i> Padrão Recomendado: ${bestMatch.name}
                    </h3>
                    <p class="text-xs text-slate-300 mt-3 leading-relaxed border-l-2 border-sgb-cyan pl-3">
                        <strong>Análise do Consultor:</strong> O seu cenário alinha-se com o padrão <strong>${bestMatch.name}</strong>. ${bestMatch.shortDesc}
                    </p>
                </div>
                <button onclick="openPatternModal('${bestMatch.id}')" class="bg-sgb-surface hover:bg-slate-700 text-sgb-cyan border border-sgb-cyan/40 font-bold py-2 px-4 rounded-lg text-xs transition-all whitespace-nowrap ml-4">
                    <i class="fa-solid fa-eye mr-1"></i> Ver Arquitetura
                </button>
            </div>
        `;
        return;
    }

    // 3. Fallback Generoso (Evita a mensagem de erro)
    const fallbackPattern = patterns.find(p => p.id === 'facade');
    cardDiv.innerHTML = `
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-sgb-orange text-xs font-bold uppercase">
                <i class="fa-solid fa-lightbulb"></i> Recomendação Genérica de Entrada
            </div>
            <p class="text-xs text-slate-300">
                Para o termo <strong>"${prompt}"</strong>, a boa prática recomendada na AWS é iniciar com o padrão <strong>Facade (API Gateway)</strong> como camada de entrada unificada para estruturar os seus microsserviços.
            </p>
            <button onclick="openPatternModal('facade')" class="bg-sgb-surface hover:bg-slate-700 text-sgb-orange border border-sgb-orange/40 font-bold py-1.5 px-3 rounded text-xs transition-all">
                Ver Padrão Facade
            </button>
        </div>
    `;
}

// --- RENDERIZAÇÃO DO CATÁLOGO E MODAIS ---
function renderSidebar() {
    const nav = document.getElementById('categoryFilters');
    nav.innerHTML = categories.map(cat => `
        <button onclick="filterPatterns('${cat.id}')" 
                class="w-full text-left flex items-center justify-between px-3 py-2.5 rounded-lg border transition-all 
                ${currentFilter === cat.id 
                    ? 'bg-sgb-orange/10 border-sgb-orange text-sgb-orange font-bold' 
                    : 'bg-transparent border-transparent text-slate-400 hover:bg-sgb-card hover:border-sgb-border'}">
            <span class="flex items-center gap-2.5 text-xs"><i class="fa-solid ${cat.icon} w-4 text-center"></i> ${cat.name}</span>
            <i class="fa-solid fa-chevron-right text-[8px] opacity-50"></i>
        </button>
    `).join('');
}

function filterPatterns(categoryId) {
    currentFilter = categoryId;
    renderSidebar();
    
    const grid = document.getElementById('patternsGrid');
    const filtered = categoryId === 'all' ? patterns : patterns.filter(p => p.category === categoryId);

    if (filtered.length === 0) {
        grid.innerHTML = `<div class="col-span-3 text-center py-10 text-slate-500 text-sm">Nenhum padrão cadastrado nesta categoria.</div>`;
        return;
    }

    grid.innerHTML = filtered.map(pattern => {
        const catName = categories.find(c => c.id === pattern.category).name;
        return `
        <div onclick="openPatternModal('${pattern.id}')" class="bg-sgb-card border border-sgb-border hover:border-sgb-orange/50 rounded-xl p-5 cursor-pointer transition-all hover:-translate-y-1 hover:shadow-lg group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-sgb-orange/10 to-transparent rounded-bl-full z-0 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="h-10 w-10 rounded-lg bg-sgb-surface border border-sgb-border flex items-center justify-center text-sgb-cyan mb-3 group-hover:text-sgb-orange transition-colors">
                    <i class="fa-solid ${pattern.icon} text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-100 text-sm mb-1">${pattern.name}</h3>
                <span class="text-[9px] font-bold uppercase tracking-wider text-sgb-orange mb-2 block">${catName}</span>
                <p class="text-[11px] text-slate-400 line-clamp-2">${pattern.shortDesc}</p>
            </div>
        </div>
        `;
    }).join('');
}

function openPatternModal(patternId) {
    const pattern = patterns.find(p => p.id === patternId);
    const catName = categories.find(c => c.id === pattern.category).name;

    document.getElementById('modalIcon').innerHTML = `<i class="fa-solid ${pattern.icon}"></i>`;
    document.getElementById('modalCategory').innerText = catName;
    document.getElementById('modalTitle').innerText = pattern.name;
    document.getElementById('modalProblem').innerText = pattern.problem;
    document.getElementById('modalSolution').innerText = pattern.solution;
    document.getElementById('modalTerraform').innerText = pattern.terraform;

    document.getElementById('modalServices').innerHTML = pattern.services.map((srv, index) => `
        <div class="flex items-center gap-3">
            <div class="bg-sgb-surface border border-sgb-border p-3 rounded-lg flex flex-col items-center justify-center w-32 shadow-md">
                <span class="text-xs font-black text-white text-center">${srv.name}</span>
                <span class="text-[9px] text-slate-400 text-center mt-1">${srv.role}</span>
            </div>
            ${index < pattern.services.length - 1 ? '<i class="fa-solid fa-arrow-right text-sgb-cyan text-sm"></i>' : ''}
        </div>
    `).join('');

    const modal = document.getElementById('patternModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePatternModal() {
    const modal = document.getElementById('patternModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('patternModal').addEventListener('click', (e) => {
    if (e.target.id === 'patternModal') closePatternModal();
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePatternModal();
});

document.addEventListener('DOMContentLoaded', () => {
    renderSidebar();
    filterPatterns('all');
});