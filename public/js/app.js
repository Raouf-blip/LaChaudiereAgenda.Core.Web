document.addEventListener("DOMContentLoaded", function () {
  fetch("/api/evenements/mois")
    .then((response) => response.json())
    .then((data) => {
      const eventsList = document.getElementById("events-list");
      if (data.length > 0) {
        data.forEach((event) => {
          const listItem = document.createElement("li");
          listItem.innerHTML = `
                        <h3>${event.title}</h3>
                        <p><strong>Artiste :</strong> ${event.artist}</p>
                        <p><strong>Date :</strong> ${new Date(
                          event.start_date
                        ).toLocaleDateString("fr-FR")}</p>
                        <p><strong>Catégorie :</strong> ${event.category}</p>
                    `;
          eventsList.appendChild(listItem);
        });
      } else {
        eventsList.innerHTML = "<p>Aucun événement prévu pour ce mois.</p>";
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des événements:", error);
      const eventsList = document.getElementById("events-list");
      eventsList.innerHTML = "<p>Erreur lors du chargement des événements.</p>";
    });
});
