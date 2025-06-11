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
    item.addEventListener("click", (e) => {
      // Prevent click from triggering if a button was clicked
      if (e.target.tagName !== "BUTTON") {
        displayEventDetails(event.id);
      }
    });

    if (event.image) {
      const img = document.createElement("img");
      img.src = `/img/${event.image.name || event.image}`;
      img.alt = event.title;
      item.appendChild(img);
    }

    const infoDiv = document.createElement("div");
    infoDiv.className = "event-info";

    const title = document.createElement("h4");
    title.className = "event-title";
    title.textContent = event.title;
    infoDiv.appendChild(title);

    const date = document.createElement("p");
    date.className = "event-date";
    date.textContent = `${event.artist} • ${new Date(
      event.start_date
    ).toLocaleDateString("fr-FR", { month: "long", day: "numeric" })}`;
    infoDiv.appendChild(date);

    item.appendChild(infoDiv);

    const buttonsDiv = document.createElement("div");
    buttonsDiv.className = "event-buttons";

    const detailsBtn = document.createElement("button");
    detailsBtn.textContent = "Détails";
    detailsBtn.addEventListener("click", (e) => {
      e.stopPropagation(); // Prevent li click event
      displayEventDetails(event.id);
    });
    buttonsDiv.appendChild(detailsBtn);

    const favoriteBtn = document.createElement("button");
    favoriteBtn.className = "favorite-button";
    const updateFavoriteButton = () => {
      const favorited = isFavorite(event.id);
      favoriteBtn.innerHTML = `<span class="heart">${
        favorited ? "❤️" : "🤍"
      }</span> ${favorited ? "Favori" : "Ajouter"}`;
      if (favorited) {
        favoriteBtn.classList.add("is-favorite");
      } else {
        favoriteBtn.classList.remove("is-favorite");
      }
    };
    updateFavoriteButton();

    favoriteBtn.addEventListener("click", (e) => {
      e.stopPropagation(); // Prevent li click event
      toggleFavorite(event.id);
      updateFavoriteButton();
      displayFavorites(); // Refresh favorites list
    });
    buttonsDiv.appendChild(favoriteBtn);

    item.appendChild(buttonsDiv);
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
      img.src = `/img/${event.image.name}`;
      img.alt = event.title;
      item.appendChild(img);
    }
    const text = document.createElement("span");
    text.textContent = `${event.title} – ${event.artist} – ${new Date(
      event.start_date
    ).toLocaleDateString()} – ${event.category.name}`;
    item.appendChild(text);
    const detailsBtn = document.createElement("button");
    detailsBtn.textContent = "Détails";
    detailsBtn.addEventListener("click", () => displayEventDetails(event.id));
    item.appendChild(detailsBtn);

    const favoriteBtn = document.createElement("button");
    favoriteBtn.className = "favorite-button is-favorite"; // Always favorite on this list
    favoriteBtn.innerHTML = `<span class="heart">❤️</span> Retirer`;
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

  container.innerHTML = ""; // Clear previous content

  const detailContent = document.createElement("div");
  detailContent.className = "event-detail-content";

  if (event.image) {
    const img = document.createElement("img");
    img.src = `/img/${event.image.name}`;
    img.alt = event.title;
    img.className = "event-detail-image";
    detailContent.appendChild(img);
  }

  const textContent = document.createElement("div");
  textContent.className = "event-detail-text";

  const title = document.createElement("h2");
  title.className = "event-detail-title";
  title.textContent = event.title;
  textContent.appendChild(title);

  const artist = document.createElement("h3");
  artist.className = "event-detail-artist";
  artist.textContent = event.artist;
  textContent.appendChild(artist);

  const infoGrid = document.createElement("div");
  infoGrid.className = "event-detail-info-grid";

  const dateInfo = document.createElement("div");
  dateInfo.className = "info-item";
  dateInfo.innerHTML = `<span>📅 Date</span><p>${new Date(
    event.start_date
  ).toLocaleDateString("fr-FR", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  })}</p>`;
  infoGrid.appendChild(dateInfo);

  const categoryInfo = document.createElement("div");
  categoryInfo.className = "info-item";
  categoryInfo.innerHTML = `<span>🏷️ Catégorie</span><p>${event.category.name}</p>`;
  infoGrid.appendChild(categoryInfo);

  if (event.price) {
    const priceInfo = document.createElement("div");
    priceInfo.className = "info-item";
    priceInfo.innerHTML = `<span>💰 Prix</span><p>${event.price} €</p>`;
    infoGrid.appendChild(priceInfo);
  }

  textContent.appendChild(infoGrid);

  const description = document.createElement("p");
  description.className = "event-detail-description";
  description.textContent = event.description;
  textContent.appendChild(description);

  detailContent.appendChild(textContent);
  container.appendChild(detailContent);

  const actionsWrapper = document.createElement("div");
  actionsWrapper.className = "event-detail-actions";

  const favoriteBtn = document.createElement("button");
  favoriteBtn.className = "favorite-button";

  const updateDetailFavoriteButton = () => {
    const favorited = isFavorite(id);
    favoriteBtn.innerHTML = `<span class="heart">${
      favorited ? "❤️" : "🤍"
    }</span> ${favorited ? "Retirer des favoris" : "Ajouter aux favoris"}`;
    if (favorited) {
      favoriteBtn.classList.add("is-favorite");
    } else {
      favoriteBtn.classList.remove("is-favorite");
    }
  };

  updateDetailFavoriteButton();

  favoriteBtn.addEventListener("click", () => {
    toggleFavorite(id);
    updateDetailFavoriteButton();
    document.getElementById("sort-options").dispatchEvent(new Event("change"));
    displayFavorites();
  });
  actionsWrapper.appendChild(favoriteBtn);

  const btn = document.createElement("button");
  btn.textContent = "Retour à la liste";
  btn.className = "back-button";
  btn.addEventListener("click", () => {
    section.style.display = "none";
    list.style.display = "block";
  });
  actionsWrapper.appendChild(btn);

  container.appendChild(actionsWrapper);
}
