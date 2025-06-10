const converter = new showdown.Converter();
const app = document.getElementById("app");

const routes = {
  "/": "home",
  "/events": "events",
  "/events/mois": "currentMonthEvents",
  "/events/:id": "eventDetails",
  "/categories/:id/events": "categoryEvents",
};

const render = (view, data) => {
  app.innerHTML = view(data);
};

const home = () => `
    <div>
        <h1>Accueil</h1>
        <p>Bienvenue sur l'agenda de La Chaudière.</p>
        <a href="/events">Voir les événements</a>
        <a href="/events/mois">Voir les événements du mois</a>
    </div>
`;

const events = async () => {
  const [eventsResponse, categoriesResponse] = await Promise.all([
    fetch("/api/evenements"),
    fetch("/api/categories"),
  ]);
  const [events, categories] = await Promise.all([
    eventsResponse.json(),
    categoriesResponse.json(),
  ]);

  const renderEvents = (events) => {
    const eventsList = document.getElementById("events-list");
    eventsList.innerHTML = events
      .map(
        (event) => `
            <li>
                <a href="/events/${event.id}">${event.title}</a>
                <span>${new Date(event.start_date).toLocaleDateString()}</span>
            </li>
        `
      )
      .join("");
  };

  setTimeout(() => {
    document
      .getElementById("category-filter")
      .addEventListener("change", async (e) => {
        const categoryId = e.target.value;
        if (categoryId) {
          const response = await fetch(
            `/api/categories/${categoryId}/evenements`
          );
          const events = await response.json();
          renderEvents(events);
        } else {
          const response = await fetch("/api/evenements");
          const events = await response.json();
          renderEvents(events);
        }
      });
    renderEvents(events);
  }, 0);

  return `
        <div>
            <h1>Événements</h1>
            <select id="category-filter">
                <option value="">Toutes les catégories</option>
                ${categories
                  .map(
                    (category) =>
                      `<option value="${category.id}">${category.name}</option>`
                  )
                  .join("")}
            </select>
            <ul id="events-list"></ul>
        </div>
    `;
};

const eventDetails = async (id) => {
  const response = await fetch(`/api/evenements/${id}`);
  const event = await response.json();
  const descriptionHtml = converter.makeHtml(event.description);
  return `
        <div>
            <h1>${event.title}</h1>
            <p><strong>Date:</strong> ${new Date(
              event.start_date
            ).toLocaleDateString()}</p>
            <p><strong>Catégorie:</strong> ${event.category.name}</p>
            <div>${descriptionHtml}</div>
        </div>
    `;
};

const categoryEvents = async (id) => {
  const response = await fetch(`/api/categories/${id}/evenements`);
  const events = await response.json();
  return `
        <div>
            <h1>Événements</h1>
            <ul>
                ${events
                  .map(
                    (event) => `
                    <li>
                        <a href="/events/${event.id}">${event.title}</a>
                        <span>${new Date(
                          event.start_date
                        ).toLocaleDateString()}</span>
                    </li>
                `
                  )
                  .join("")}
            </ul>
        </div>
    `;
};

const currentMonthEvents = async () => {
  const response = await fetch("/api/evenements/mois");
  const events = await response.json();
  return `
        <div>
            <h1>Événements du mois</h1>
            <ul>
                ${events
                  .map(
                    (event) => `
                    <li>
                        <h3>${event.title}</h3>
                        <p><strong>Artiste:</strong> ${event.artist}</p>
                        <p><strong>Date:</strong> ${new Date(
                          event.start_date
                        ).toLocaleDateString()}</p>
                        <p><strong>Catégorie:</strong> ${event.category}</p>
                    </li>
                `
                  )
                  .join("")}
            </ul>
        </div>
    `;
};

const router = async () => {
  const path = window.location.pathname;
  const route = Object.keys(routes).find((r) => {
    const regex = new RegExp(`^${r.replace(/:\w+/g, "(\\w+)")}$`);
    return regex.test(path);
  });

  if (route) {
    const viewName = routes[route];
    const regex = new RegExp(`^${route.replace(/:\w+/g, "(\\w+)")}$`);
    const params = path.match(regex).slice(1);
    const view = await window[viewName](...params);
    render(() => view);
  } else {
    render(() => `<h1>404 Not Found</h1>`);
  }
};

window.addEventListener("popstate", router);

document.addEventListener("DOMContentLoaded", () => {
  document.body.addEventListener("click", (e) => {
    if (e.target.matches("a")) {
      e.preventDefault();
      history.pushState(null, null, e.target.href);
      router();
    }
  });
  router();
});

window.home = home;
window.events = events;
window.eventDetails = eventDetails;
window.categoryEvents = categoryEvents;
window.currentMonthEvents = currentMonthEvents;
