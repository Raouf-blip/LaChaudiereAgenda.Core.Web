import { loadCategories, loadEventsByCategory, loadEvents } from "./api.js";
import { displayEvents, displayFavorites, displayEventDetails } from "./ui.js";

document.addEventListener("DOMContentLoaded", async () => {
  await setupEventListeners();
  await displayCategories();
  await setupCategoryFilter();
  await setupSortOptions();
  const events = await loadEvents();
  displayEvents(events);
  displayFavorites();
});

async function setupEventListeners() {
  const showFavoritesBtn = document.getElementById("show-favorites");
  const hideFavoritesBtn = document.getElementById("hide-favorites");
  const eventsSection = document.getElementById("evenements");
  const favoritesSection = document.getElementById("favoris");

  showFavoritesBtn.addEventListener("click", () => {
    eventsSection.style.display = "none";
    favoritesSection.style.display = "block";
  });

  hideFavoritesBtn.addEventListener("click", () => {
    eventsSection.style.display = "block";
    favoritesSection.style.display = "none";
  });
}

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
    const sort = document.getElementById("sort-options").value;
    const container = document.getElementById("events-list");
    container.innerHTML = "Chargement...";

    let events;
    if (!categoryId) {
      events = await loadEvents({ sort });
    } else {
      events = await loadEventsByCategory(categoryId, sort);
    }

    displayEvents(events);
  });
}

async function setupSortOptions() {
  const select = document.getElementById("sort-options");
  if (!select) return;

  select.addEventListener("change", async (e) => {
    const sort = e.target.value;
    const categoryId = document.getElementById("category-filter").value;
    const container = document.getElementById("events-list");
    container.innerHTML = "Chargement...";

    let events;
    if (categoryId) {
      events = await loadEventsByCategory(categoryId, sort);
    } else {
      events = await loadEvents({ sort });
    }

    displayEvents(events);
  });
}

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
      const eventsContainer = document.getElementById("events-list");
      document.getElementById("evenements").style.display = "block";
      document.getElementById("details").style.display = "none";
      document.getElementById("favoris").style.display = "none";
      eventsContainer.innerHTML = "Chargement...";
      const events = await loadEventsByCategory(cat.id);
      displayEvents(events);
    });
  });
}
