import {
  loadCategories,
  loadEventsByCategory,
  loadEvents,
  loadCurrentEvents,
} from "./api.js";
import { displayEvents, displayFavorites, displayEventDetails } from "./ui.js";

document.addEventListener("DOMContentLoaded", async () => {
  await setupEventListeners();
  await setupFilters();
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

async function setupFilters() {
  const select = document.getElementById("category-filter");
  const categoryListContainer = document.getElementById("categorie-list");
  if (!select || !categoryListContainer) return;

  const categories = await loadCategories();

  select.innerHTML = '<option value="">Toutes les catégories</option>';
  categories.forEach((cat) => {
    const option = document.createElement("option");
    option.value = cat.id;
    option.textContent = cat.name;
    select.appendChild(option);
  });

  categoryListContainer.innerHTML = "";
  const allCategoriesDiv = document.createElement("div");
  allCategoriesDiv.textContent = "Toutes";
  allCategoriesDiv.classList.add("category-item");
  allCategoriesDiv.addEventListener("click", () => {
    select.value = "";
    select.dispatchEvent(new Event("change"));
  });
  categoryListContainer.appendChild(allCategoriesDiv);

  categories.forEach((cat) => {
    const div = document.createElement("div");
    div.textContent = cat.name;
    div.classList.add("category-item");
    div.addEventListener("click", () => {
      select.value = cat.id;
      select.dispatchEvent(new Event("change"));
    });
    categoryListContainer.appendChild(div);
  });

  select.addEventListener("change", async (e) => {
    document.getElementById("evenements").style.display = "block";
    document.getElementById("favoris").style.display = "none";
    document.getElementById("details").style.display = "none";

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
