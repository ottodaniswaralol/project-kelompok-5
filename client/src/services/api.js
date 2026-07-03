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

// GET ROOMS
export async function getRooms() {
  const response = await fetch(`${BASE_URL}/rooms/list.php`);
  return response.json();
}

// CREATE BOOKING
export async function createBooking(data) {
  const response = await fetch(`${BASE_URL}/booking/create.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  return response.json();
}

// CREATE RECURRING BOOKING
export async function createRecurringBooking(data) {
  const response = await fetch(`${BASE_URL}/booking/create_recurring.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  return response.json();
}

// GET ANALYTICS
export async function getAnalytics(month, year) {
  const response = await fetch(`${BASE_URL}/reports/analytics.php?month=${month}&year=${year}`);
  return response.json();
}

// EXPORT CSV
export function getExportCSVUrl(month, year) {
  return `${BASE_URL}/reports/export.php?format=csv&month=${month}&year=${year}`;
}

// GET MY BOOKING HISTORY
export async function getMyBookingHistory(userId) {
  const response = await fetch(`${BASE_URL}/booking/list.php?user_id=${userId}`);
  return response.json();
}

// GET APPROVAL LIST
export async function getApprovalList(role) {
  const response = await fetch(`${BASE_URL}/approval/list.php?role=${role}`);
  return response.json();
}

// APPROVE
export async function approveBooking(approval_id, approver_id, notes = '') {
  const response = await fetch(`${BASE_URL}/approval/approve.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ approval_id, approver_id, notes }),
  });
  return response.json();
}

// REJECT
export async function rejectBooking(approval_id, approver_id, notes) {
  const response = await fetch(`${BASE_URL}/approval/reject.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ approval_id, approver_id, notes }),
  });
  return response.json();
}