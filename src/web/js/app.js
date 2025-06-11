import {loadCategories, loadEventsByCategory, loadCurrentEvents, loadEvents} from './api.js';


document.addEventListener("DOMContentLoaded", async () => {
    await displayCategories();                  // fonctionnalité 3
    await displayCurrentMonthEvents();          // fonctionnalité 1
    await setupCategoryFilter();                // fonctionnalité 2
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
    events.forEach(event => {
        const item = document.createElement("li");
        item.textContent = `${event.title} – ${event.artist} – ${new Date(event.start_date).toLocaleDateString()} – ${event.category}`;
        container.appendChild(item);
    });
}

// Fonctionnalité 2 : filtre par catégorie via <select>
async function setupCategoryFilter() {
    const select = document.getElementById("category-filter");
    if (!select) return;

    const categories = await loadCategories();
    categories.forEach(cat => {
        const option = document.createElement("option");
        option.value = cat.id;
        option.textContent = cat.name;
        select.appendChild(option);
    });

    select.addEventListener("change", async (e) => {
        const categoryId = e.target.value;
        const container = document.getElementById("events-list");
        container.innerHTML = "Chargement...";


        const events = await loadEventsByCategory(categoryId);
        if (!events.length) {
            container.textContent = "Aucun événement pour cette catégorie.";
            return;
        }

        container.innerHTML = "";
        events.forEach(event => {
            const item = document.createElement("li");
            item.textContent = `${event.title} – ${event.artist} – ${new Date(event.start_date).toLocaleDateString()} – ${event.category}`;
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
    categories.forEach(cat => {
        const div = document.createElement("div");
        div.textContent = cat.name;
        div.classList.add("category-item");
        container.appendChild(div);

        div.addEventListener("click", async () => {
            const eventsContainer = document.getElementById("events-list");
            eventsContainer.innerHTML = "Chargement...";
            const events = await loadEventsByCategory(cat.id);

            if (!events.length) {
                eventsContainer.textContent = "Aucun événement pour cette catégorie.";
                return;
            }

            eventsContainer.innerHTML = "";
            events.forEach(event => {
                const item = document.createElement("li");
                item.textContent = `${event.title} – ${event.artist} – ${new Date(event.start_date).toLocaleDateString()} – ${event.category}`;
                eventsContainer.appendChild(item);
            });
        });
    });
}
