const FAVORITES_KEY = "favoriteEvents";

export function getFavorites() {
  const favorites = localStorage.getItem(FAVORITES_KEY);
  return favorites ? JSON.parse(favorites) : [];
}

export function toggleFavorite(eventId) {
  let favorites = getFavorites();
  if (favorites.includes(eventId)) {
    favorites = favorites.filter((id) => id !== eventId);
  } else {
    favorites.push(eventId);
  }
  localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
}

export function isFavorite(eventId) {
  const favorites = getFavorites();
  return favorites.includes(eventId);
}
