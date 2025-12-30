// src/services/api.js

// 1. BASE_URL cukup sampai folder 'server' atau 'api' saja
const BASE_URL = "https://project-kelompok-5-production.up.railway.app/api/";

// LOGIN
export async function login(username, password) {
  // 2. Di sini baru lu sambungin ke path filenya yang bener
  const response = await fetch(`${BASE_URL}auth/login.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      username: username,
      password: password,
    }),
  });

  return response.json();
}

// GET ROOMS
export async function getRooms() {
  const response = await fetch(`${BASE_URL}/rooms/list.php`);
  return response.json();
}

// CREATE BOOKING
export async function createBooking(data) {
  const response = await fetch(`${BASE_URL}/booking/create.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }
  );

  return response.json();
}