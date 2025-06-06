import { loadEvenementsCourants } from './api.js';

document.addEventListener("DOMContentLoaded", async () => {
    const container = document.getElementById("categorie-list");

    const data = await loadEvenementsCourants();
    if (!data) {
        container.textContent = "Erreur de chargement.";
        return;
    }

    container.innerHTML = "";
    data.forEach(evt => {
        const div = document.createElement("div");
        div.textContent = `${evt.title} - ${evt.start_date} - ${evt.category}`;

        container.appendChild(div);
    });
});

