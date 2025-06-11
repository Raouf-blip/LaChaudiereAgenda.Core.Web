const BASE_URL = "http://localhost:10000/api";

export async function loadCategories() {
  try {
    const response = await fetch(`${BASE_URL}/categories`);
    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
    return await response.json();
  } catch (error) {
    console.error("Erreur de chargement des catégories :", error);
    return [];
  }
}

export async function loadEvents(params = {}) {
  try {
    const url = new URL(`${BASE_URL}/evenements`);
    if (params.sort) {
      url.searchParams.append("sort", params.sort);
    }
    const response = await fetch(url);
    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
    return await response.json();
  } catch (error) {
    console.error("Erreur de chargement des événements :", error);
    return [];
  }
}

export async function loadEventsByCategory(id, sort = null) {
  try {
    const url = new URL(`${BASE_URL}/categories/${id}/evenements`);
    if (sort) {
      url.searchParams.append("sort", sort);
    }
    const response = await fetch(url);
    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
    return await response.json();
  } catch (error) {
    console.error(
      `Erreur de chargement des événements de la catégorie ${id} :`,
      error
    );
    return [];
  }
}

export async function loadEventDetails(id) {
  try {
    const response = await fetch(`${BASE_URL}/evenements/${id}`);
    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
    return await response.json();
  } catch (error) {
    console.error(
      `Erreur de chargement du détail de l’événement ${id} :`,
      error
    );
    return null;
  }
}

export async function loadCurrentEvents() {
  try {
    const response = await fetch(`${BASE_URL}/evenements/mois`);
    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
    return await response.json();
  } catch (error) {
    console.error("Erreur de chargement des événements du mois :", error);
    return [];
  }
}
