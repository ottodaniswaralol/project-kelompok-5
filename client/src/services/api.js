// Cukup sampai folder 'api' saja agar fleksibel
const BASE_URL = "https://project-kelompok-5-production.up.railway.app/api";

// LOGIN
export async function login(username, password) {
  const response = await fetch(`${BASE_URL}/auth/login.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, password }),
  });
  return response.json();
}

// GET ROOMS (Jalur otomatis benar: /api/rooms/list.php)
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