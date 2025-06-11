import {
  loadCategories,
  loadEventsByCategory,
  loadCurrentEvents,
  loadEvents,
  loadEventDetails,
} from "./api.js";

document.addEventListener("DOMContentLoaded", async () => {
  await displayCategories(); // fonctionnalité 3
  await displayCurrentMonthEvents(); // fonctionnalité 1
  await setupCategoryFilter(); // fonctionnalité 2
});

// Fonctionnalité 1 : événements du mois courant
async function displayCurrentMonthEvents() {
  const container = document.getElementById("events-list");
  container.innerHTML = "Chargement...";
  const events = await loadEvents();

  if (!events.length) {
    container.textContent = "Aucun événement prévu pour ce mois.";
    return;
  }

  container.innerHTML = "";
  events.forEach((event) => {
    const item = document.createElement("li");
    item.textContent = `${event.title} – ${event.artist} – ${new Date(
      event.start_date
    ).toLocaleDateString()} – ${event.category}`;
    const btn = document.createElement("button");
    btn.textContent = "Détails";
    btn.addEventListener("click", () => displayEventDetails(event.id));

    item.appendChild(btn);
    container.appendChild(item);
  });
}

// Fonctionnalité 2 : filtre par catégorie via <select>
async function setupCategoryFilter() {
  const select = document.getElementById("category-filter");
  if (!select) return;

  const categories = await loadCategories();
  categories.forEach((cat) => {
    const option = document.createElement("option");
    option.value = cat.id;
    option.textContent = cat.name;
    select.appendChild(option);
  });

  select.addEventListener("change", async (e) => {
    const categoryId = e.target.value;
    const container = document.getElementById("events-list");
    container.innerHTML = "Chargement...";

    let events;
    if (!categoryId) {
      // Si aucune catégorie sélectionnée, charger tous les événements
      events = await loadEvents();
    } else {
      // Charger les événements par catégorie
      events = await loadEventsByCategory(categoryId);
    }

    if (!events.length) {
      container.textContent = "Aucun événement pour cette catégorie.";
      return;
    }

    container.innerHTML = "";
    events.forEach((event) => {
      const item = document.createElement("li");
      item.textContent = `${event.title} – ${event.artist} – ${new Date(
        event.start_date
      ).toLocaleDateString()} – ${event.category}`;
      const btn = document.createElement("button");
      btn.textContent = "Détails";
      btn.addEventListener("click", () => displayEventDetails(event.id));
      item.appendChild(btn);
      container.appendChild(item);
    });
  });
}

// Fonctionnalité 3 : liste des catégories cliquables
async function displayCategories() {
  const container = document.getElementById("categorie-list");
  const categories = await loadCategories();

  if (!categories.length) {
    container.textContent = "Aucune catégorie trouvée.";
    return;
  }

  container.innerHTML = "";
  categories.forEach((cat) => {
    const div = document.createElement("div");
    div.textContent = cat.name;
    div.classList.add("category-item");
    container.appendChild(div);

    div.addEventListener("click", async () => {
      const eventsContainer = document.getElementById("events-list"); // C’est le bon UL dans la section "Événements"

      // 🟢 Afficher la bonne section
      document.getElementById("evenements").style.display = "block";
      document.getElementById("details").style.display = "none";

      eventsContainer.innerHTML = "Chargement...";

      const events = await loadEventsByCategory(cat.id, "courante");

      if (!events.length) {
        eventsContainer.textContent = "Aucun événement pour cette catégorie.";
        return;
      }

      eventsContainer.innerHTML = "";

      events.forEach((event) => {
        const item = document.createElement("li");
        item.textContent = `${event.title} – ${event.artist} – ${new Date(
          event.start_date
        ).toLocaleDateString()} – ${event.category}`;

        const btn = document.createElement("button");
        btn.textContent = "Détails";
        btn.addEventListener("click", () => displayEventDetails(event.id));

        item.appendChild(btn);
        eventsContainer.appendChild(item);
      });
    });
  });
}

// Fonctionnalité 4 : détails d'un événement
async function displayEventDetails(id) {
  const container = document.getElementById("evenement-details");
  const section = document.getElementById("details");
  const list = document.getElementById("evenements");

  container.textContent = "Chargement...";
  section.style.display = "block";
  list.style.display = "none";

  const event = await loadEventDetails(id);
  if (!event) {
    container.textContent = "Erreur lors du chargement.";
    return;
  }

  container.textContent = `${event.title} - ${event.artist} - ${new Date(
    event.start_date
  ).toLocaleDateString()} - ${event.category.name} - ${event.description}`;

  const btn = document.createElement("button");
  btn.textContent = "Retour";
  btn.addEventListener("click", () => {
    section.style.display = "none";
    list.style.display = "block";
  });

  container.appendChild(document.createElement("br"));
  container.appendChild(btn);
}
