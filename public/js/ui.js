import { loadEventDetails } from "./api.js";
import { getFavorites, toggleFavorite, isFavorite } from "./favorites.js";

export function displayEvents(events) {
  const container = document.getElementById("events-list");
  container.innerHTML = "";

  if (!events.length) {
    container.textContent = "Aucun événement ne correspond à votre recherche.";
    return;
  }

  events.forEach((event) => {
    const item = document.createElement("li");
    if (event.image) {
      const img = document.createElement("img");
      img.src = `/img/${event.image}`;
      img.alt = event.title;
      item.appendChild(img);
    }
    const text = document.createElement("span");
    text.textContent = `${event.title} – ${new Date(
      event.start_date
    ).toLocaleDateString()} – ${event.category}`;
    item.appendChild(text);
    const detailsBtn = document.createElement("button");
    detailsBtn.textContent = "Détails";
    detailsBtn.addEventListener("click", () => displayEventDetails(event.id));
    item.appendChild(detailsBtn);

    const favoriteBtn = document.createElement("button");
    favoriteBtn.textContent = isFavorite(event.id)
      ? "Retirer des favoris"
      : "Ajouter aux favoris";
    favoriteBtn.addEventListener("click", () => {
      toggleFavorite(event.id);
      displayEvents(events);
      displayFavorites();
    });
    item.appendChild(favoriteBtn);

    container.appendChild(item);
  });
}

export async function displayFavorites() {
  const container = document.getElementById("favorites-list");
  container.innerHTML = "Chargement...";
  const favoriteIds = getFavorites();

  if (!favoriteIds.length) {
    container.textContent = "Vous n'avez aucun événement en favori.";
    return;
  }

  const favoriteEvents = await Promise.all(
    favoriteIds.map((id) => loadEventDetails(id))
  );

  container.innerHTML = "";
  favoriteEvents.forEach((event) => {
    const item = document.createElement("li");
    if (event.image) {
      const img = document.createElement("img");
      img.src = `/img/${event.image}`;
      img.alt = event.title;
      item.appendChild(img);
    }
    const text = document.createElement("span");
    text.textContent = `${event.title} – ${new Date(
      event.start_date
    ).toLocaleDateString()} – ${event.category.name}`;
    item.appendChild(text);
    const detailsBtn = document.createElement("button");
    detailsBtn.textContent = "Détails";
    detailsBtn.addEventListener("click", () => displayEventDetails(event.id));
    item.appendChild(detailsBtn);

    const favoriteBtn = document.createElement("button");
    favoriteBtn.textContent = "Retirer des favoris";
    favoriteBtn.addEventListener("click", () => {
      toggleFavorite(event.id);
      displayFavorites();
      // Refresh the main event list to update its favorite buttons
      document
        .getElementById("sort-options")
        .dispatchEvent(new Event("change"));
    });
    item.appendChild(favoriteBtn);

    container.appendChild(item);
  });
}

export async function displayEventDetails(id) {
  const container = document.getElementById("evenement-details");
  const section = document.getElementById("details");
  const list = document.getElementById("evenements");
  const favorites = document.getElementById("favoris");

  container.textContent = "Chargement...";
  section.style.display = "block";
  list.style.display = "none";
  favorites.style.display = "none";

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
