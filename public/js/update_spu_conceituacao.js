const SUPABASE_URL = "https://rzdmnzuweyzhilfcungl.supabase.co";
const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJ6ZG1uenV3ZXl6aGlsZmN1bmdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODE4NTk5NTcsImV4cCI6MjA5NzQzNTk1N30.IqRxw3n2c-zNCccbgOUfh7wLy8eNnOVKJzwa8AsoSnU";

const opcoes = [
  "Terreno/acrescido de marinha",
  "Terreno/acrescido marginal",
  "Nacional interior"
];

async function updateConceituacao() {
  for (let i = 1; i <= 20; i++) {
    const num = `2026${String(i).padStart(3, '0')}`;
    const conceituacao = opcoes[(i - 1) % 3];

    const getRes = await fetch(`${SUPABASE_URL}/rest/v1/tabela_spu?numero_rip=eq.${num}`, {
      headers: { 'apikey': SUPABASE_ANON_KEY, 'Authorization': `Bearer ${SUPABASE_ANON_KEY}` }
    });

    if (!getRes.ok) { console.error(`Erro buscar ${num}:`, await getRes.text()); continue; }
    const rows = await getRes.json();
    if (rows.length === 0) { console.warn(`RIP ${num} não encontrado.`); continue; }

    const dados = rows[0].dados_json || {};
    dados.conceituacao = conceituacao;

    const patchRes = await fetch(`${SUPABASE_URL}/rest/v1/tabela_spu?numero_rip=eq.${num}`, {
      method: 'PATCH',
      headers: {
        'apikey': SUPABASE_ANON_KEY,
        'Authorization': `Bearer ${SUPABASE_ANON_KEY}`,
        'Content-Type': 'application/json',
        'Prefer': 'return=minimal'
      },
      body: JSON.stringify({ dados_json: dados })
    });

    if (patchRes.ok) {
      console.log(`RIP ${num} → ${conceituacao}`);
    } else {
      console.error(`Erro patch ${num}:`, await patchRes.text());
    }
  }
  console.log("Concluído!");
}

updateConceituacao();
