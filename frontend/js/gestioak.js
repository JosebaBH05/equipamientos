// URLs del backend
const urlKokapena = "../backend/controladores/gestioakController.php";
const urlEquipos = "../backend/controladores/ekipamenduakController.php";
const urlGelas = "../backend/controladores/gelakController.php";

// Elementos DOM
const listKokapena = document.getElementById("kokapena-list");
const selectEquipo = document.getElementById("select-equipo");
const selectGela = document.getElementById("select-gela");

// --- Listar Kokapena ---
async function listarKokapena() {
  listKokapena.innerHTML = '<li class="data-row text-center py-2">Kargatzen...</li>';
  try {
    const res = await fetch(urlKokapena + "?action=GET");
    const data = await res.json();
    listKokapena.innerHTML = '';

    if (data.success && data.data.length > 0) {
      const fragment = document.createDocumentFragment();
      data.data.forEach((k, idx) => {
        const li = document.createElement("li");
        li.className = `data-row ${idx % 2 === 0 ? 'even' : ''}`;
        li.innerHTML = `
          <span>${k.ekipamendu_izena}</span>
          <span>${k.gela_izena}</span>
          <span>${k.taldea}</span>
          `;
        fragment.appendChild(li);
      });
      listKokapena.appendChild(fragment);
    } else {
      listKokapena.innerHTML = '<li class="data-row empty">Ez dago daturik.</li>';
    }
  } catch (err) {
    console.error(err);
    listKokapena.innerHTML = '<li class="data-row empty">Errorea konektatzean.</li>';
  }
}

// --- Cargar Selects ---
async function cargarSelect(url, select, placeholder) {
  select.innerHTML = `<option value="">${placeholder}</option>`;
  try {
    const res = await fetch(url + "?action=GET");
    const data = await res.json();
    if (!data.success) throw new Error("Error al cargar datos");

    data.data.forEach(item => {
      const option = document.createElement("option");
      option.value = item.id;
      option.textContent = item.izena;
      select.appendChild(option);
    });
  } catch (err) {
    console.error(err);
    select.innerHTML += `<option value="">Errorea kargatzean</option>`;
  }
}

// --- Abrir modal: cargar selects ---
const addBtn = document.querySelector("[data-bs-target='#addKokapenaModal']");
addBtn.addEventListener("click", () => {
  cargarSelect(urlEquipos, selectEquipo, "Aukeratu ekipoa");
  cargarSelect(urlGelas, selectGela, "Aukeratu gela");
});

// --- Guardar Kokapena ---
document.getElementById("guardar-kokapena-btn").addEventListener("click", async () => {
  const idEquipo = selectEquipo.value;
  const idGela = selectGela.value;

  if (!idEquipo || !idGela) {
    alert("Aukeratu ekipamendu eta gela bat!");
    return;
  }

  try {
    const res = await fetch(urlKokapena, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "INSERT",
      ekipamendu_id: idEquipo,
      gela_id: idGela
    })
    });

    const data = await res.json();
    if (data.success) {
      alert("Kokapena gordeta!");
      listarKokapena();
      const modal = bootstrap.Modal.getInstance(document.getElementById("addKokapenaModal"));
      modal.hide();
    } else {
      alert("Errorea gordetzean: " + data.message);
    }
    } catch (err) {
      console.error(err);
      alert("Errorea zerbitzariarekin konektatzean.");
    }
});

// ============================================================
  // BÚSQUEDA GLOBAL DE EQUIPOS (IGNORA MAYÚSCULAS Y LA CABECERA)
  // ============================================================
  const searchInput = document.getElementById("searchInput");

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const filtro = searchInput.value.trim().toLowerCase();
      const contenedor = document.getElementById("kokapena-container");

      if (!contenedor) return;

      // Seleccionamos todas las filas excepto la cabecera
      const filas = contenedor.querySelectorAll(".data-row:not(.data-header)");

      filas.forEach(fila => {
        const texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
      });
    });
  }

// Inicializar al cargar
document.addEventListener("DOMContentLoaded", listarKokapena);