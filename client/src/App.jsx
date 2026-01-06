import React, { useState, useEffect } from 'react';
import { login, createBooking, getRooms } from "./services/api";

/**
 * UTILS & CONFIG
 */
// Fungsi untuk menangani jika logo utama gagal load, ganti ke cadangan
const handleLogoError = (e) => {
  e.target.onerror = null; // Mencegah loop infinit
  // Link cadangan resmi dari website Bakrie
  e.target.src = "https://lpkm.bakrie.ac.id/assets/img/logo-ub.png"; 
};

const LOGO_PRIMARY_URL = "https://upload.wikimedia.org/wikipedia/commons/a/a0/Universitas_Bakrie_Logo.svg";

// --- COMPONENTS: ICONS (SVG) ---
const Icons = {
  Check: () => <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" /></svg>,
  X: () => <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>,
  Home: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>,
  Document: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>,
  Clock: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>,
  Search: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>,
  Trash: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>,
  Logout: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>,
  ChevronLeft: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>,
};

// --- COMPONENTS: UI ELEMENTS ---

const LoadingOverlay = ({ message = "Loading..." }) => (
  <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center backdrop-blur-sm animate-fade-in">
    <div className="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center gap-4">
      <div className="w-10 h-10 border-4 border-gray-200 border-t-[#990000] rounded-full animate-spin"></div>
      <p className="text-sm font-semibold text-gray-700 animate-pulse">{message}</p>
    </div>
  </div>
);

const ModalSuccess = ({ onClose, title, message }) => (
  <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm animate-fade-in p-4">
    <div className="bg-white rounded-lg shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-100 ring-1 ring-gray-200">
      <div className="bg-green-50 p-6 flex flex-col items-center text-center border-b border-green-100">
        <div className="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4 shadow-sm">
          <Icons.Check />
        </div>
        <h3 className="text-xl font-bold text-gray-800">{title}</h3>
      </div>
      <div className="p-6 text-center">
        <p className="text-gray-600 mb-6 text-sm leading-relaxed">{message}</p>
        <button 
          onClick={onClose} 
          className="w-full bg-[#1a1a1a] hover:bg-black text-white py-3 rounded text-sm font-bold transition-transform active:scale-95"
        >
          Tutup / Lanjutkan
        </button>
      </div>
    </div>
  </div>
);

const InventoryCounter = ({ label }) => {
  const [count, setCount] = useState(0);
  const [isActive, setIsActive] = useState(false);

  useEffect(() => { if (!isActive) setCount(0); }, [isActive]);

  return (
    <div className={`flex items-center gap-4 p-3 rounded border transition-all duration-200 ${isActive ? 'bg-blue-50 border-blue-200 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300'}`}>
      <div className="flex items-center gap-3 w-48 shrink-0">
        <input type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} className="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" />
        <span className={`text-sm font-medium transition-colors ${isActive ? 'text-gray-900' : 'text-gray-500'}`}>{label}</span>
      </div>
      <div className="flex items-center bg-white rounded border border-gray-300 overflow-hidden shadow-sm">
        <button disabled={!isActive} onClick={() => setCount(Math.max(0, count - 1))} className="px-3 py-1 hover:bg-gray-100 disabled:opacity-50 border-r border-gray-300 text-gray-600">-</button>
        <div className="w-10 text-center text-sm font-semibold text-gray-700">{count}</div>
        <button disabled={!isActive} onClick={() => setCount(count + 1)} className="px-3 py-1 hover:bg-gray-100 disabled:opacity-50 border-l border-gray-300 text-gray-600">+</button>
      </div>
      <input type="text" placeholder={isActive ? "Keterangan" : "Centang untuk isi"} disabled={!isActive} className="flex-1 text-xs border-b border-gray-300 bg-transparent py-1 px-2 focus:border-blue-500 focus:outline-none disabled:text-transparent transition-all"/>
    </div>
  );
};

// --- SUB-PAGES ---

const FormPengajuan = ({ user, onSubmitData, onBack }) => {
  const [formData, setFormData] = useState({ eventName: '', orgName: '', date: '', room: '', startTime: '', endTime: '', pic: '', phone: '', notes: '' });
  const [loadingCheck, setLoadingCheck] = useState(false);
  const [availabilityStatus, setAvailabilityStatus] = useState(null);
  const [formErrors, setFormErrors] = useState({});
  
  // STATE BARU: Untuk menyimpan daftar ruangan asli dari DB
  const [roomList, setRoomList] = useState([]); 

  // EFFECT BARU: Ambil daftar ruangan dari Database saat halaman dibuka
  useEffect(() => {
    const fetchRooms = async () => {
      try {
        const data = await getRooms(); // Panggil API list.php
        setRoomList(data); // Simpan ke state
      } catch (error) {
        console.error("Gagal ambil room:", error);
      }
    };
    fetchRooms();
  }, []);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    if (name === 'date' || name === 'room') setAvailabilityStatus(null);
    if (formErrors[name]) setFormErrors(prev => ({ ...prev, [name]: '' }));
  };

  const handleCheckAvailability = async () => {
    if (!formData.date || !formData.room) return alert("Pilih Tanggal dan Ruangan dulu.");
    
    setLoadingCheck(true);
    setAvailabilityStatus(null); 
    
    try {
      console.log(`Mengecek: Tanggal ${formData.date}, Ruangan ${formData.room}`);

      const res = await fetch(`https://project-kelompok-5-production.up.railway.app/api/booking/check_availability.php?date=${formData.date}&room=${encodeURIComponent(formData.room)}`);
      const json = await res.json();
      
      console.log("Hasil Cek:", json); // <--- CEK DISINI (F12)

      if (json.status === 'booked') {
        setAvailabilityStatus('booked');
      } else if (json.status === 'available') {
        setAvailabilityStatus('available');
      } else {
        // Kalau status error, tampilkan alert biar sadar
        alert("Error Backend: " + json.message);
      }

    } catch (error) {
      console.error("Gagal connect:", error);
      alert("Gagal terhubung ke server backend.");
    } finally {
      setLoadingCheck(false);
    }
  };

  const validate = () => {
    let errors = {};
    if (!formData.eventName) errors.eventName = "Wajib diisi";
    if (!formData.orgName) errors.orgName = "Wajib diisi";
    if (!formData.date) errors.date = "Wajib dipilih";
    if (!formData.room) errors.room = "Wajib dipilih";
    if (!formData.startTime) errors.startTime = "Wajib diisi";
    if (!formData.pic) errors.pic = "Wajib diisi";
    return errors;
  };

  return (
    <div className="animate-fade-in pb-10">
      <div className="flex items-center justify-between mb-6">
        <div><h2 className="text-2xl font-bold text-gray-800">Formulir Peminjaman</h2><p className="text-sm text-gray-500">Lengkapi data untuk mengajukan peminjaman.</p></div>
        <button onClick={onBack} className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold flex items-center gap-2 transition"><Icons.ChevronLeft /> Kembali</button>
      </div>
      <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div className="bg-blue-50 p-4 border-b border-blue-100 flex gap-3 items-start"><div className="text-blue-500 mt-0.5"><Icons.Document /></div><div><h4 className="text-sm font-bold text-blue-800">Informasi Penting</h4><p className="text-xs text-blue-700 mt-1">Cek ketersediaan ruangan sebelum submit.</p></div></div>
        <div className="p-8 space-y-8">
          
          {/* SECTION I */}
          <section>
            <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">I. Detail Kegiatan</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan *</label><input type="text" name="eventName" value={formData.eventName} onChange={handleChange} className={`w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 outline-none ${formErrors.eventName ? 'border-red-500 ring-red-200' : 'border-gray-300 ring-blue-100'}`}/></div>
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Organisasi *</label><input type="text" name="orgName" value={formData.orgName} onChange={handleChange} className={`w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 outline-none ${formErrors.orgName ? 'border-red-500 ring-red-200' : 'border-gray-300 ring-blue-100'}`}/></div>
              <div className="md:col-span-2"><label className="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label><textarea className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none h-20 resize-none"></textarea></div>
            </div>
          </section>

          {/* SECTION II - RUANGAN (INI YANG KITA PERBAIKI) */}
          <section>
            <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">II. Waktu & Tempat</h3>
            <div className="bg-gray-50 p-6 rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label><input type="date" name="date" value={formData.date} onChange={handleChange} className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.date ? 'border-red-500' : 'border-gray-300'}`}/></div>
                <div>
                   <label className="block text-sm font-medium text-gray-700 mb-1">Ruangan *</label>
                   <div className="flex gap-2">
                     {/* UPDATE: Dropdown sekarang pakai roomList dari Database */}
                     <select name="room" value={formData.room} onChange={handleChange} className={`flex-1 border rounded-lg px-3 py-2 text-sm bg-white ${formErrors.room ? 'border-red-500' : 'border-gray-300'}`}>
                       <option value="">-- Pilih Ruangan --</option>
                       {roomList.map((r) => (
                         // Kita pakai room_name sebagai value biar cocok sama Backend check_availability
                         <option key={r.id || r.room_id} value={r.room_name}>
                           {r.room_name} 
                         </option>
                       ))}
                     </select>
                     <button onClick={handleCheckAvailability} disabled={loadingCheck} className="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-xs px-4 rounded-lg font-medium transition-colors shadow-sm whitespace-nowrap">{loadingCheck ? '...' : 'Cek'}</button>
                   </div>
                   <div className="mt-2 min-h-[20px]">
                      {availabilityStatus === 'available' && <p className="text-xs text-green-600 font-bold flex items-center gap-1"><Icons.Check /> Tersedia!</p>}
                      {availabilityStatus === 'booked' && <p className="text-xs text-red-600 font-bold flex items-center gap-1"><Icons.X /> Penuh / Booked</p>}
                   </div>
                </div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Mulai *</label><input type="time" name="startTime" value={formData.startTime} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Selesai</label><input type="time" name="endTime" value={formData.endTime} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
            </div>
          </section>

          {/* SECTION III & IV */}
          <section>
             <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">III. Kontak</h3>
             <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">PIC *</label><input type="text" name="pic" value={formData.pic} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label><input type="text" name="phone" value={formData.phone} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
             </div>
          </section>

          <div className="pt-6 border-t border-gray-100 flex justify-end gap-4">
              <button onClick={() => { if(window.confirm('Reset?')) setFormData({ eventName: '', orgName: '', date: '', room: '', startTime: '', endTime: '', pic: '', phone: '', notes: '' }); }} className="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-bold hover:bg-gray-50 transition">Reset</button>
              <button onClick={() => { const errors = validate(); if (Object.keys(errors).length > 0) { setFormErrors(errors); return; } onSubmitData(formData); }} className="px-8 py-2.5 rounded-lg bg-[#990000] text-white font-bold">Submit Pengajuan</button>
          </div>
        </div>
      </div>
    </div>
  );
};

// --- COMPONENT STATUS TABLE (FINAL & BERSIH) ---
const StatusTable = ({ bookings, onBack, onCancel }) => {
  return (
    <div className="animate-fade-in pb-10">
      {/* Header Halaman */}
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Status Pengajuan</h2>
          <p className="text-sm text-gray-500">Pantau status persetujuan peminjaman ruangan Anda.</p>
        </div>
        <button onClick={onBack} className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold flex items-center gap-2 transition">
          <Icons.ChevronLeft /> Kembali
        </button>
      </div>

      <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col min-h-[400px]">
        
        {/* Search Bar & Total Count */}
        <div className="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
          <div className="relative">
             <input type="text" placeholder="Cari Kegiatan..." className="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 w-64" />
             <div className="absolute left-3 top-2.5 text-gray-400"><Icons.Search /></div>
          </div>
          <div className="text-xs text-gray-500 font-medium">Total: <span className="text-gray-900 font-bold">{bookings.length}</span></div>
        </div>

        {/* Tabel Data */}
        <div className="overflow-x-auto flex-1">
          <table className="w-full text-left border-collapse">
            <thead className="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider font-semibold">
              <tr>
                <th className="px-6 py-4 border-b w-16">ID</th>
                <th className="px-6 py-4 border-b w-1/4">Waktu & Ruangan</th>
                <th className="px-6 py-4 border-b w-1/4">Kegiatan</th>
                <th className="px-6 py-4 border-b w-1/5">Notes</th>
                <th className="px-6 py-4 border-b text-center">Status</th>
                <th className="px-6 py-4 border-b text-center">Aksi</th>
              </tr>
            </thead>
            <tbody className="text-sm text-gray-700 divide-y divide-gray-100">
              {bookings.length === 0 ? (
                <tr>
                  <td colSpan="6">
                    <div className="flex flex-col items-center justify-center h-64 text-gray-400">
                      <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3"><Icons.Document /></div>
                      <p>Belum ada data pengajuan.</p>
                    </div>
                  </td>
                </tr>
              ) : (
                bookings.map((item) => (
                  <tr key={item.id} className="hover:bg-blue-50/50 transition-colors group">
                    <td className="px-6 py-4 font-mono text-xs text-gray-500">#{item.id}</td>
                    
                    <td className="px-6 py-4">
                      <div className="font-bold text-gray-800">{item.date}</div>
                      <div className="text-xs text-gray-500 flex items-center gap-1 mt-1">
                         <Icons.Clock /> {item.time}
                      </div>
                      <span className="inline-block mt-2 bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                        {item.room}
                      </span>
                    </td>

                    <td className="px-6 py-4">
                      <div className="font-bold text-[#990000] line-clamp-2">{item.event}</div>
                      <div className="text-xs text-gray-600 mt-0.5">{item.org}</div>
                    </td>

                    <td className="px-6 py-4 text-xs text-gray-500 italic">
                      {item.notes ? item.notes : "-"}
                    </td>

                    <td className="px-6 py-4 text-center">
                      <span className={`inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border ${
                        item.status === 'Disetujui' ? 'bg-green-100 text-green-700 border-green-200' : 
                        item.status === 'Ditolak' ? 'bg-red-100 text-red-700 border-red-200' : 
                        'bg-yellow-100 text-yellow-700 border-yellow-200'
                      }`}>
                        {item.status}
                      </span>
                    </td>

                    <td className="px-6 py-4 text-center">
                      <button 
                        onClick={() => onCancel(item.id)} 
                        className="text-red-500 hover:text-white hover:bg-red-600 border border-red-200 hover:border-red-600 px-3 py-1.5 rounded transition-all text-xs font-bold flex items-center gap-1 mx-auto"
                        title="Batalkan Pengajuan"
                      >
                        <Icons.Trash /> Batalkan
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
      {/* PASTIKAN TIDAK ADA KODE {isModalOpen && ...} DI BAWAH SINI! */}
    </div>
  );
};

// --- GANTI SELURUH COMPONENT PeminjamanPage DENGAN INI ---
const PeminjamanPage = ({ user, onBackToMenu, onToDashboard }) => {
  const [activeTab, setActiveTab] = useState('pengajuan');
  const [bookings, setBookings] = useState([]); // State Data Tabel
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showSuccessModal, setShowSuccessModal] = useState(false);

  // 1. FUNGSI AMBIL DATA (GET) DARI BACKEND
  const fetchBookings = async () => {
    try {
      // Ambil ID dari data login (user)
      // Kalau user null (belum login), pakai 0 biar data kosong
      const userId = user?.id || user?.user_id || 0; 
      
      const res = await fetch(`https://project-kelompok-5-production.up.railway.app/api/booking/list.php?user_id=${userId}`);
      const data = await res.json();
      
      if (Array.isArray(data)) {
        setBookings(data);
      }
    } catch (error) {
      console.error("Gagal refresh data:", error);
    }
  };

  // 2. JALANKAN SAAT HALAMAN DIBUKA (Auto Refresh)
  useEffect(() => {
    fetchBookings();
  }, []);

  // 3. FUNGSI SUBMIT DENGAN AUTO-REFRESH TABEL
  const handleSubmit = async (formData) => {
    setIsSubmitting(true);
    
    // Susun data menjadi Object JSON (Bukan FormData) agar sesuai dengan api.js
    const payload = {
      user_id: user.id || user.user_id || 1,
      event_name: formData.eventName,
      organization: formData.orgName,
      pic: formData.pic,
      phone: formData.phone,
      event_description: formData.notes || "",
      start_datetime: `${formData.date} ${formData.startTime}`,
      end_datetime: `${formData.date} ${formData.endTime || formData.startTime}`,
      rooms: [formData.room], // Kirim sebagai array sesuai format backend
      inventory: []
    };

    try {
      // PENTING: Gunakan fungsi createBooking dari api.js, jangan fetch manual!
      const data = await createBooking(payload);

      if (data.status === "success") {
        // SUKSES!
        setShowSuccessModal(true); 
        fetchBookings(); // Refresh tabel
      } else {
        alert("Gagal dari server: " + (data.message || "Unknown error"));
      }

    } catch (error) {
      console.error("Error submit:", error);
      alert("Koneksi Gagal / Error CORS. Cek Console F12.");
    } finally {
      setIsSubmitting(false);
    }
  };

  // --- LOGIC HAPUS DATA (DEBUGGING VERSION) ---
  const handleCancelBooking = async (id) => {
    // 1. Cek di Console Browser (F12) apakah ID-nya muncul?
    console.log("Mau menghapus ID:", id); 

    if (!id) {
        alert("Error: ID Booking tidak valid (Undefined).");
        return;
    }

    if (!window.confirm(`Yakin ingin menghapus booking #${id}?`)) return;

    try {
      const res = await fetch("https://project-kelompok-5-production.up.railway.app/api/booking/delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id }) // Kirim ID
      });

      const text = await res.text(); // Ambil text dulu buat jaga-jaga kalau error HTML
      console.log("Respon Server:", text);

      try {
          const data = JSON.parse(text);
          if (data.status === "success") {
            alert("✅ Berhasil dihapus!");
            fetchBookings(); // Refresh tabel
          } else {
            alert("❌ Gagal: " + data.message);
          }
      } catch (e) {
          alert("❌ Server Error: " + text);
      }
    } catch (error) {
      console.error(error);
      alert("❌ Gagal terhubung ke server.");
    }
  };

  const handleCloseModal = () => { 
      setShowSuccessModal(false); 
      setActiveTab('status'); // Otomatis pindah ke tab Status
  };

  return (
    <div className="flex h-screen bg-gray-50 overflow-hidden font-sans">
      {/* SIDEBAR */}
      <aside className="w-64 bg-[#7a1e1e] text-white flex flex-col shrink-0 shadow-2xl z-20">
        <div className="p-6 border-b border-red-900/50 flex items-center gap-3 bg-[#601010]">
          <div className="bg-white p-1.5 rounded shadow-sm">
             <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="w-8 h-auto" />
          </div>
          <div><h1 className="font-bold text-sm tracking-wide">Biro Kemahasiswaan</h1><p className="text-[10px] text-red-200">Universitas Bakrie</p></div>
        </div>
        <nav className="flex-1 overflow-y-auto py-6 space-y-1">
          <div className="px-6 text-[10px] font-bold text-red-300 uppercase tracking-widest mb-2">Main Menu</div>
          <button onClick={() => setActiveTab('pengajuan')} className={`w-full text-left px-6 py-3 flex items-center gap-3 transition-all border-l-4 ${activeTab === 'pengajuan' ? 'bg-[#990000] border-white shadow-inner' : 'border-transparent text-red-100 hover:bg-[#852020]'}`}><Icons.Document /><span className="font-medium text-sm">Form Pengajuan</span></button>
          <button onClick={() => setActiveTab('status')} className={`w-full text-left px-6 py-3 flex items-center gap-3 transition-all border-l-4 ${activeTab === 'status' ? 'bg-[#990000] border-white shadow-inner' : 'border-transparent text-red-100 hover:bg-[#852020]'}`}><div className="relative"><Icons.Search />{bookings.length > 0 && <span className="absolute -top-1 -right-1 w-2 h-2 bg-yellow-400 rounded-full animate-ping"></span>}</div><span className="font-medium text-sm">Status Pengajuan</span></button>
          <div className="mt-8 px-6 text-[10px] font-bold text-red-300 uppercase tracking-widest mb-2">System</div>
          <button onClick={onBackToMenu} className="w-full text-left px-6 py-3 flex items-center gap-3 text-red-100 hover:bg-[#852020] transition-all border-l-4 border-transparent"><Icons.ChevronLeft /> <span className="font-medium text-sm">Kembali ke Menu</span></button>
        </nav>
      </aside>

      {/* MAIN CONTENT */}
      <main className="flex-1 flex flex-col h-full relative overflow-hidden">
        {/* HEADER (FIX: NAMA & ROLE MUNCUL DISINI) */}
        <header className="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-8 z-10 shrink-0">
           <div className="text-sm breadcrumbs text-gray-500"><span className="cursor-pointer hover:text-[#990000]" onClick={onToDashboard}>Home</span><span className="mx-2">/</span><span className="font-bold text-[#990000]">Peminjaman Ruangan</span></div>
           
           {/* Profil User di Kanan Atas */}
           <div className="flex items-center gap-4">
              <div className="text-right hidden md:block">
                <p className="text-sm font-bold text-gray-800">Hi, {user?.name || "User"}</p>
                <p className="text-xs text-gray-500 capitalize">{user?.role || "Mahasiswa"}</p>
              </div>
              <div className="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-bold shadow-sm">
                {user?.name?.charAt(0).toUpperCase() || "U"}
              </div>
           </div>
        </header>

        {/* ISI HALAMAN */}
        <div className="flex-1 overflow-y-auto p-8 relative custom-scrollbar">
           {activeTab === "pengajuan" ? (
             <FormPengajuan user={user} onSubmitData={handleSubmit} onBack={onBackToMenu} />
           ) : (
             // Tabel Status (Data diambil dari state 'bookings')
             <StatusTable bookings={bookings} onBack={() => setActiveTab("pengajuan")} onCancel={handleCancelBooking} />
           )}
        </div>
      </main>
      
      {/* MODAL & LOADING */}
      {isSubmitting && <LoadingOverlay message="Mengirim Data..." onForceClose={() => setIsSubmitting(false)} />}
      {showSuccessModal && (<ModalSuccess title="Pengajuan Berhasil!" message="Data telah disubmit. Silakan pantau status di tabel ini." onClose={handleCloseModal} />)}
    </div>
  );
};

// --- COMPONENT HALAMAN MENU BIMA ---
const BimaPage = ({ onBack, onNavigate }) => {
  const menus = [
    { label: "Pendanaan Kompetisi", disabled: true }, 
    { label: "Asuransi Mahasiswa", disabled: true }, 
    { label: "Beasiswa & Bantuan", disabled: true }, 
    { label: "Layanan Psikologi", disabled: true }, 
    { label: "Student Exit Letter", disabled: true },
    { label: "Peminjaman Fasilitas Kampus", action: 'peminjaman', highlight: true },
    { label: "Buku Panduan", disabled: true }, 
    { label: "Surat Keterangan Aktif", disabled: true }, 
    { label: "Transkrip Nilai Non-Akademik", disabled: true },
  ];

  return (
    <div className="min-h-screen bg-white font-sans flex flex-col animate-fade-in">
      {/* Header Kecil */}
      <div className="h-2 bg-[#1a1a1a] w-full"></div>
      <header className="bg-[#990000] px-8 py-4 shadow-lg flex justify-between items-center sticky top-0 z-30">
        <div className="flex items-center gap-4">
           <div className="bg-white p-1.5 rounded shadow-sm">
             <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="h-10 w-auto" />
           </div>
           <div className="text-white">
             <h1 className="text-xl font-bold tracking-wide">Biro Kemahasiswaan</h1>
             <p className="text-xs text-white/80">Integrated System</p>
           </div>
        </div>
        <button onClick={onBack} className="text-white/90 hover:text-white border border-white/30 hover:bg-white/10 px-4 py-2 rounded text-sm font-medium transition">
          Kembali ke Dashboard
        </button>
      </header>

      {/* Grid Menu */}
      <div className="flex-1 max-w-7xl mx-auto w-full p-8 md:p-12">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-extrabold text-gray-900 mb-3">Layanan Kemahasiswaan</h2>
          <p className="text-gray-500">Pilih layanan yang Anda butuhkan.</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {menus.map((item, idx) => (
            <div 
              key={idx} 
              onClick={() => item.action ? onNavigate(item.action) : alert("Fitur ini akan segera hadir.")} 
              className={`relative h-40 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center p-6 text-center border ${item.highlight ? 'bg-[#1a1a1a] border-black' : 'bg-white border-gray-100 hover:border-gray-300'}`}
            >
              {item.highlight && (
                <div className="absolute inset-0 opacity-20 pointer-events-none">
                  {/* Pattern Background */}
                  <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 L100 0 L100 100 Z" fill="white" /></svg>
                </div>
              )}
              <div className={`mb-3 p-3 rounded-full ${item.highlight ? 'bg-white/10 text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-[#990000] group-hover:text-white transition-colors'}`}>
                {item.highlight ? <Icons.Document /> : <Icons.Home />}
              </div>
              <h3 className={`font-bold text-lg ${item.highlight ? 'text-white' : 'text-gray-800'}`}>
                {item.label}
              </h3>
              {item.disabled && <span className="absolute top-3 right-3 text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded">Soon</span>}
            </div>
          ))}
        </div>
      </div>

      {/* Footer */}
      <footer className="bg-[#1a1a1a] text-white py-8 border-t border-gray-800">
         <div className="max-w-7xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div className="flex items-center gap-4">
               <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="h-8 grayscale brightness-200" />
               <div className="text-xs text-gray-400">
                 <p className="font-bold text-white uppercase">Universitas Bakrie</p>
                 <p>Jl. HR Rasuna Said Kav C-22, Jakarta Selatan</p>
               </div>
            </div>
            <p className="text-xs text-gray-500">&copy; 2025 Biro Administrasi Pembelajaran</p>
         </div>
      </footer>
    </div>
  );
};

const Dashboard = ({ user, onLogout, onNavigate }) => {
  const modules = [
    { title: "BIG", color: "bg-[#c0392b]", icon: "BIG" }, { title: "E-Learning", color: "bg-[#2980b9]", icon: "LMS" }, { title: "Parent Portal", color: "bg-[#d35400]", icon: "PRT" },
    { title: "Perpustakaan", color: "bg-[#27ae60]", icon: "LIB" }, { title: "Info PMB", color: "bg-[#f39c12]", icon: "PMB" }, { title: "BIMA (Kemahasiswaan)", color: "bg-[#800000]", icon: "BIMA", action: 'bima', featured: true },
  ];

  return (
    <div className="min-h-screen bg-gray-100 font-sans flex items-center justify-center p-4 md:p-8 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
      <div className="w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col min-h-[80vh]">
        <div className="bg-[#990000] text-white p-6 flex justify-between items-center shadow-lg relative z-10">
          <div className="flex items-center gap-5">
             <div className="bg-white p-2 rounded-lg shadow-md transform hover:rotate-3 transition duration-300">
               {/* LOGO DENGAN FALLBACK */}
               <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo UB" className="h-10 w-auto" />
             </div>
             <div><h1 className="font-extrabold text-2xl tracking-wide uppercase">BIG 2.0</h1><p className="text-xs text-red-200 tracking-wider">Bakrie Information Gateway</p></div>
          </div>
          <div className="flex items-center gap-4">
             <div className="hidden md:block text-right mr-2"><p className="text-sm font-bold">Halo, {user?.name}</p><p className="text-[10px] text-red-200">Senin, 29 Des 2025</p></div>
             <button onClick={onLogout} className="bg-white/10 hover:bg-white hover:text-[#990000] text-white border border-white/30 px-5 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2"><Icons.Logout /> Logout</button>
          </div>
        </div>
        <div className="flex-1 p-8 md:p-12 overflow-y-auto bg-gray-50">
           <div className="mb-8 border-b border-gray-200 pb-4"><h2 className="text-2xl font-bold text-gray-800">Daftar Modul Aplikasi</h2><p className="text-gray-500 mt-1">Silakan pilih modul yang ingin Anda akses.</p></div>
           <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
             {modules.map((modul, idx) => (
               <div key={idx} onClick={() => modul.action ? onNavigate(modul.action) : alert("Maintenance.")} className={`${modul.color} group relative h-48 rounded-xl shadow-lg cursor-pointer transform hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between p-6`}>
                 <div className="absolute -right-10 -bottom-10 w-40 h-40 bg-white opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                 <div className="relative z-10"><span className="bg-white/20 text-white px-2 py-1 rounded text-[10px] font-bold tracking-widest uppercase mb-2 inline-block">System</span><h3 className="text-white text-xl font-bold leading-tight">{modul.title}</h3></div>
                 <div className="relative z-10 flex justify-between items-end"><div className="text-white/60 text-xs font-mono">Ver 2.5.0</div><div className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white backdrop-blur-sm group-hover:bg-white group-hover:text-gray-800 transition-colors"><svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></div></div>
               </div>
             ))}
           </div>
        </div>
        <div className="bg-white p-4 text-center text-xs text-gray-400 border-t border-gray-200">&copy; 2025 Universitas Bakrie • IT Directorate</div>
      </div>
    </div>
  );
};

const LoginPage = ({ onLogin, loading }) => {
  const [user, setUser] = useState(''); const [pass, setPass] = useState(''); const [showPass, setShowPass] = useState(false);
  const handleSubmit = (e) => { e.preventDefault(); onLogin(user, pass); };
  return (
    <div className="min-h-screen flex flex-col md:flex-row bg-white font-sans overflow-hidden">
      <div className="hidden md:flex md:w-[60%] lg:w-[65%] relative bg-slate-900 flex-col justify-between text-white overflow-hidden">
         <div className="absolute inset-0 z-0"><img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop" alt="Campus" className="w-full h-full object-cover opacity-60 mix-blend-overlay" /><div className="absolute inset-0 bg-gradient-to-br from-[#990000] via-[#5e0d0d] to-black opacity-90"></div></div>
         <div className="relative z-10 p-16 flex flex-col h-full justify-between">
            <div>
               <div className="flex items-center gap-3 mb-8">
                  <div className="w-12 h-12 bg-white rounded-lg p-2 flex items-center justify-center shadow-lg">
                     {/* LOGO DENGAN FALLBACK */}
                     <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="w-full h-auto" />
                  </div>
                  <h1 className="text-xl font-bold tracking-widest uppercase">Universitas Bakrie</h1>
               </div>
               <h2 className="text-5xl font-extrabold leading-tight mb-6">Experience Real <br/> Education.</h2>
               <p className="text-lg text-red-100 max-w-md font-light">Bergabunglah dengan ekosistem digital kami. Akses seluruh layanan akademik dan kemahasiswaan.</p>
            </div>
            <div className="space-y-6"><div className="flex items-center gap-4"><div className="flex -space-x-4">{[1,2,3,4].map(i => (<div key={i} className="w-10 h-10 rounded-full border-2 border-[#990000] bg-gray-300 overflow-hidden"><img src={`https://i.pravatar.cc/100?img=${i+10}`} alt="User" /></div>))}</div><p className="text-sm font-medium">Bergabung bersama <br/> 5000+ Mahasiswa Aktif</p></div><div className="h-px bg-white/20 w-full"></div><div className="flex gap-8 text-xs font-bold tracking-widest text-red-200 uppercase"><span>#ExperienceTheRealThings</span><span>#Unggul</span></div></div>
         </div>
      </div>
      <div className="flex-1 flex flex-col items-center justify-center p-8 md:p-12 relative">
         <div className="w-full max-w-md">
            <div className="text-center mb-10"><h2 className="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2><p className="text-gray-500">Silakan masuk ke akun BIG 2.0 Anda</p></div>
            <form onSubmit={handleSubmit} className="space-y-6">
               <div><label className="block text-sm font-bold text-gray-700 mb-2">Username / NIM</label><input type="text" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#990000] outline-none" placeholder="NIM Anda" value={user} onChange={(e) => setUser(e.target.value)}/></div>
               <div><label className="block text-sm font-bold text-gray-700 mb-2">Kata Sandi</label><div className="relative"><input type={showPass ? "text" : "password"} required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#990000] outline-none" placeholder="••••••••" value={pass} onChange={(e) => setPass(e.target.value)}/><button type="button" onClick={() => setShowPass(!showPass)} className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-xs font-bold">{showPass ? "SEMBUNYIKAN" : "LIHAT"}</button></div></div>
               <button type="submit" disabled={loading} className="w-full bg-[#990000] hover:bg-[#7a0000] text-white font-bold py-3.5 rounded-lg shadow-lg transition-all transform active:scale-95 flex justify-center items-center">{loading ? "Memproses..." : "MASUK APLIKASI"}</button>
            </form>
            <div className="mt-10 text-center text-xs text-gray-400"><p>&copy; 2025 Universitas Bakrie.</p></div>
         </div>
      </div>
    </div>
  );
};

function App() {
  const [user, setUser] = useState(() => {
  const saved = localStorage.getItem("user");
  return saved ? JSON.parse(saved) : null;
});

  const [loading, setLoading] = useState(false);
  const [currentPage, setCurrentPage] = useState('dashboard');

  const handleLogin = async (username, password) => {
  setLoading(true);
  try {
    const res = await login(username, password);

    if (res.status) {
      localStorage.setItem("user", JSON.stringify(res.user));
      setUser(res.user);
      setCurrentPage("dashboard");
    } else {
      alert(res.message || "Login gagal");
    }
  } catch (err) {
    alert("Gagal konek ke server");
  } finally {
    setLoading(false);
  }
};

  const handleLogout = () => {
  if (window.confirm("Keluar dari aplikasi?")) {
    localStorage.removeItem("user");
    setUser(null);
    setCurrentPage("dashboard");
  }
};

  if (!user) return <LoginPage onLogin={handleLogin} loading={loading} />;
  if (currentPage === 'peminjaman') return <PeminjamanPage
  user={user}
  onBackToMenu={() => setCurrentPage("bima")}
  onToDashboard={() => setCurrentPage("dashboard")}
/>
;
  if (currentPage === 'bima') return <BimaPage onBack={() => setCurrentPage('dashboard')} onNavigate={setCurrentPage} />;
  return (
  <Dashboard
    user={user}
    onLogout={handleLogout}
    onNavigate={setCurrentPage}
  />
);
}

export default App;