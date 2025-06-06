const URL = 'http://localhost:10000/api';
export async function loadEvenementsCourants() {
    const url = `${URL}/evenements?periode=courante`;
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("Erreur HTTP " + response.status);
        return await response.json();
    } catch (error) {
        console.error("Erreur de chargement des événements :", error);
        return null;
    }
}
