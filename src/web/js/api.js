const URL = 'http://localhost:10000/api';

export async function loadCategories() {
    try {
        const response = await fetch(`${URL}/categories`);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error("Erreur de chargement des catégories :", error);
        return [];
    }
}

export async function loadEvents() {
    try {
        const response = await fetch(`${URL}/evenements`);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error("Erreur de chargement des événements :", error);
        return [];
    }
}

export async function loadEventsByCategory(id) {
    try {
        const response = await fetch(`${URL}/categories/${id}/evenements`);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error(`Erreur de chargement des événements de la catégorie ${id} :`, error);
        return [];
    }
}

export async function loadEventDetails(id) {
    try {
        const response = await fetch(`${URL}/evenements/${id}`);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error(`Erreur de chargement du détail de l’événement ${id} :`, error);
        return null;
    }
}

export async function loadCurrentEvents() {
    try {
        const response = await fetch(`${URL}/evenements/mois`);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error("Erreur de chargement des événements du mois :", error);
        return [];
    }
}
