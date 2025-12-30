// src/services/api.js

const BASE_URL = "http://localhost/peminjaman_ruangan_backend/api";

// LOGIN
export async function login(username, password) {
  const response = await fetch(`${BASE_URL}/auth/login.php`, {
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
  const response = await fetch(
    `${BASE_URL}/booking/create.php`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }
  );

  return response.json();
}
