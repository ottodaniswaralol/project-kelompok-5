import React, { useState, useEffect } from 'react';
import { login, createBooking, getRooms, createRecurringBooking, getAnalytics, getExportCSVUrl } from "./services/api";

// ===================== UTILS =====================
const handleLogoError = (e) => {
  e.target.onerror = null;
  e.target.src = "https://lpkm.bakrie.ac.id/assets/img/logo-ub.png";
};
const LOGO_PRIMARY_URL = "https://upload.wikimedia.org/wikipedia/commons/a/a0/Universitas_Bakrie_Logo.svg";

// ===================== ICONS =====================
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
  Chart: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>,
  Repeat: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>,
  Download: () => <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>,
};

// ===================== UI COMPONENTS =====================
const LoadingOverlay = ({ message = "Loading..." }) => (
  <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center backdrop-blur-sm">
    <div className="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center gap-4">
      <div className="w-10 h-10 border-4 border-gray-200 border-t-[#990000] rounded-full animate-spin"></div>
      <p className="text-sm font-semibold text-gray-700">{message}</p>
    </div>
  </div>
);

const ModalSuccess = ({ onClose, title, message }) => (
  <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div className="bg-white rounded-lg shadow-2xl w-full max-w-sm overflow-hidden">
      <div className="bg-green-50 p-6 flex flex-col items-center text-center border-b border-green-100">
        <div className="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
          <Icons.Check />
        </div>
        <h3 className="text-xl font-bold text-gray-800">{title}</h3>
      </div>
      <div className="p-6 text-center">
        <p className="text-gray-600 mb-6 text-sm leading-relaxed">{message}</p>
        <button onClick={onClose} className="w-full bg-[#1a1a1a] hover:bg-black text-white py-3 rounded text-sm font-bold transition">
          Tutup / Lanjutkan
        </button>
      </div>
    </div>
  </div>
);

// ===================== ANALYTICS DASHBOARD =====================
const AnalyticsPage = ({ user, onBack }) => {
  const now = new Date();
  const [month, setMonth] = useState(now.getMonth() + 1);
  const [year, setYear]   = useState(now.getFullYear());
  const [data, setData]   = useState(null);
  const [loading, setLoading] = useState(false);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await getAnalytics(month, year);
      setData(res);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, [month, year]);

  const maxBooking = data?.chart_data?.length > 0
    ? Math.max(...data.chart_data.map(d => parseInt(d.total_bookings)))
    : 1;

  const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

  return (
    <div className="flex h-screen bg-gray-50 overflow-hidden font-sans">
      {/* SIDEBAR */}
      <aside className="w-64 bg-[#7a1e1e] text-white flex flex-col shrink-0 shadow-2xl z-20">
        <div className="p-6 border-b border-red-900/50 flex items-center gap-3 bg-[#601010]">
          <div className="bg-white p-1.5 rounded shadow-sm">
            <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="w-8 h-auto" />
          </div>
          <div><h1 className="font-bold text-sm tracking-wide">UBakrie Space</h1><p className="text-[10px] text-red-200">Analytics</p></div>
        </div>
        <nav className="flex-1 py-6 space-y-1">
          <div className="px-6 text-[10px] font-bold text-red-300 uppercase tracking-widest mb-2">Menu</div>
          <button className="w-full text-left px-6 py-3 flex items-center gap-3 bg-[#990000] border-l-4 border-white">
            <Icons.Chart /><span className="font-medium text-sm">Dashboard Analitik</span>
          </button>
          <button onClick={onBack} className="w-full text-left px-6 py-3 flex items-center gap-3 text-red-100 hover:bg-[#852020] border-l-4 border-transparent">
            <Icons.ChevronLeft /><span className="font-medium text-sm">Kembali</span>
          </button>
        </nav>
      </aside>

      {/* MAIN */}
      <main className="flex-1 flex flex-col overflow-hidden">
        <header className="h-16 bg-white shadow-sm border-b flex items-center justify-between px-8 shrink-0">
          <h2 className="text-lg font-bold text-gray-800">Dashboard Analitik Ruangan</h2>
          <div className="flex items-center gap-3">
            <div className="text-right hidden md:block">
              <p className="text-sm font-bold text-gray-800">{user?.name}</p>
              <p className="text-xs text-gray-500 capitalize">{user?.role}</p>
            </div>
            <div className="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-bold">
              {user?.name?.charAt(0).toUpperCase() || "U"}
            </div>
          </div>
        </header>

        <div className="flex-1 overflow-y-auto p-8 space-y-6">

          {/* FILTER */}
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-wrap gap-4 items-end justify-between">
            <div className="flex gap-4">
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Bulan</label>
                <select value={month} onChange={e => setMonth(parseInt(e.target.value))}
                  className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 outline-none">
                  {MONTHS.map((m, i) => <option key={i} value={i+1}>{m}</option>)}
                </select>
              </div>
              <div>
                <label className="block text-xs font-bold text-gray-500 mb-1">Tahun</label>
                <select value={year} onChange={e => setYear(parseInt(e.target.value))}
                  className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 outline-none">
                  {[2025, 2026, 2027].map(y => <option key={y} value={y}>{y}</option>)}
                </select>
              </div>
            </div>
            <a href={getExportCSVUrl(month, year)} target="_blank" rel="noreferrer"
              className="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
              <Icons.Download /> Export CSV
            </a>
          </div>

          {/* SUMMARY CARDS */}
          {data?.summary && (
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[
                { label: 'Total Booking', value: data.summary.total_booking, color: 'bg-blue-50 text-blue-700 border-blue-100' },
                { label: 'Disetujui', value: data.summary.total_approved, color: 'bg-green-50 text-green-700 border-green-100' },
                { label: 'Ditolak', value: data.summary.total_rejected, color: 'bg-red-50 text-red-700 border-red-100' },
                { label: 'Pending', value: data.summary.total_pending, color: 'bg-yellow-50 text-yellow-700 border-yellow-100' },
              ].map((card, i) => (
                <div key={i} className={`rounded-xl border p-5 ${card.color}`}>
                  <p className="text-xs font-bold uppercase tracking-wider opacity-70">{card.label}</p>
                  <p className="text-3xl font-black mt-1">{card.value || 0}</p>
                </div>
              ))}
            </div>
          )}

          {/* BAR CHART */}
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 className="text-sm font-bold text-gray-700 mb-6">
              Booking per Ruangan — {MONTHS[month-1]} {year}
            </h3>
            {loading ? (
              <div className="flex justify-center py-12">
                <div className="w-8 h-8 border-4 border-gray-200 border-t-red-600 rounded-full animate-spin"></div>
              </div>
            ) : data?.chart_data?.length > 0 ? (
              <div className="space-y-3">
                {data.chart_data.map((item, i) => (
                  <div key={i} className="flex items-center gap-4">
                    <div className="w-32 text-xs font-medium text-gray-600 text-right shrink-0">{item.room_name}</div>
                    <div className="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                      <div
                        className="h-full bg-[#990000] rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                        style={{ width: `${(parseInt(item.total_bookings) / maxBooking) * 100}%` }}
                      >
                        <span className="text-[10px] font-bold text-white">{item.total_bookings}</span>
                      </div>
                    </div>
                    <div className="w-20 text-xs text-gray-500 shrink-0">{item.total_minutes_used} menit</div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-12 text-gray-400">
                <Icons.Chart />
                <p className="mt-2 text-sm">Tidak ada data approved untuk periode ini</p>
              </div>
            )}
          </div>

          {/* POPULARITAS RUANGAN */}
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 className="text-sm font-bold text-gray-700 mb-4">Pola Penggunaan Ruangan (Top 10)</h3>
            {data?.popularity?.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="text-xs text-gray-500 uppercase border-b">
                      <th className="pb-3 text-left">Ruangan</th>
                      <th className="pb-3 text-left">Hari</th>
                      <th className="pb-3 text-left">Jam</th>
                      <th className="pb-3 text-center">Total Booking</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {data.popularity.map((item, i) => (
                      <tr key={i} className="hover:bg-gray-50">
                        <td className="py-3 font-medium text-gray-800">{item.room_name}</td>
                        <td className="py-3 text-gray-600">{item.day_name}</td>
                        <td className="py-3 text-gray-600">{item.hour_of_day}:00</td>
                        <td className="py-3 text-center">
                          <span className="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">{item.booking_count}</span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <p className="text-center text-gray-400 text-sm py-8">Belum ada data pola penggunaan</p>
            )}
          </div>

        </div>
      </main>
    </div>
  );
};

// ===================== FORM PENGAJUAN (dengan Recurring) =====================
const FormPengajuan = ({ user, onSubmitData, onSubmitRecurring, onBack }) => {
  const [formData, setFormData] = useState({
    eventName: '', orgName: '', date: '', room: '',
    startTime: '', endTime: '', pic: '', phone: '', notes: ''
  });
  const [isRecurring, setIsRecurring] = useState(false);
  const [recurringData, setRecurringData] = useState({
    day_of_week: 1, start_date: '', end_date: '', frequency: 'weekly'
  });
  const [loadingCheck, setLoadingCheck]       = useState(false);
  const [availabilityStatus, setAvailabilityStatus] = useState(null);
  const [formErrors, setFormErrors]           = useState({});
  const [roomList, setRoomList]               = useState([]);

  useEffect(() => {
    const fetchRooms = async () => {
      try {
        const data = await getRooms();
        setRoomList(data);
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

  const handleRecurringChange = (e) => {
    const { name, value } = e.target;
    setRecurringData(prev => ({ ...prev, [name]: value }));
  };

  const handleCheckAvailability = async () => {
    if (!formData.date || !formData.room) return alert("Pilih Tanggal dan Ruangan dulu.");
    setLoadingCheck(true);
    setAvailabilityStatus(null);
    try {
      const res = await fetch(`https://project-kelompok-5-production.up.railway.app/api/booking/check_availability.php?date=${formData.date}&room=${encodeURIComponent(formData.room)}`);
      const json = await res.json();
      setAvailabilityStatus(json.status === 'booked' ? 'booked' : 'available');
    } catch {
      alert("Gagal terhubung ke server.");
    } finally {
      setLoadingCheck(false);
    }
  };

  const validate = () => {
    let errors = {};
    if (!formData.eventName) errors.eventName = "Wajib diisi";
    if (!formData.orgName)   errors.orgName   = "Wajib diisi";
    if (!formData.room)      errors.room      = "Wajib dipilih";
    if (!formData.startTime) errors.startTime = "Wajib diisi";
    if (!formData.pic)       errors.pic       = "Wajib diisi";
    if (!isRecurring && !formData.date) errors.date = "Wajib dipilih";
    if (isRecurring) {
      if (!recurringData.start_date) errors.start_date = "Wajib diisi";
      if (!recurringData.end_date)   errors.end_date   = "Wajib diisi";
    }
    return errors;
  };

  const handleSubmit = () => {
    const errors = validate();
    if (Object.keys(errors).length > 0) { setFormErrors(errors); return; }

    if (isRecurring) {
      const selectedRoom = roomList.find(r => r.room_name === formData.room);
      onSubmitRecurring({
        user_id:      user?.user_id || user?.id || 1,
        room_id:      selectedRoom?.room_id || selectedRoom?.id,
        event_name:   formData.eventName,
        organization: formData.orgName,
        phone:        formData.phone,
        description:  formData.notes,
        day_of_week:  parseInt(recurringData.day_of_week),
        start_date:   recurringData.start_date,
        end_date:     recurringData.end_date,
        start_time:   formData.startTime,
        end_time:     formData.endTime,
        frequency:    recurringData.frequency,
        interval_count: 1,
      });
    } else {
      onSubmitData(formData);
    }
  };

  const DAYS = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

  return (
    <div className="pb-10">
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Formulir Peminjaman</h2>
          <p className="text-sm text-gray-500">Lengkapi data untuk mengajukan peminjaman.</p>
        </div>
        <button onClick={onBack} className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold flex items-center gap-2 transition">
          <Icons.ChevronLeft /> Kembali
        </button>
      </div>

      <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div className="p-8 space-y-8">

          {/* TOGGLE RECURRING */}
          <div className="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
            <div className="flex items-center gap-3">
              <Icons.Repeat />
              <div>
                <p className="text-sm font-bold text-gray-800">Peminjaman Rutin</p>
                <p className="text-xs text-gray-500">Aktifkan untuk booking berulang (mingguan)</p>
              </div>
            </div>
            <button
              onClick={() => setIsRecurring(!isRecurring)}
              className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${isRecurring ? 'bg-[#990000]' : 'bg-gray-300'}`}
            >
              <span className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow ${isRecurring ? 'translate-x-6' : 'translate-x-1'}`} />
            </button>
          </div>

          {/* SECTION I: Detail Kegiatan */}
          <section>
            <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">I. Detail Kegiatan</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan *</label>
                <input type="text" name="eventName" value={formData.eventName} onChange={handleChange}
                  className={`w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 outline-none ${formErrors.eventName ? 'border-red-500' : 'border-gray-300'}`} />
                {formErrors.eventName && <p className="text-red-500 text-xs mt-1">{formErrors.eventName}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Organisasi *</label>
                <input type="text" name="orgName" value={formData.orgName} onChange={handleChange}
                  className={`w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 outline-none ${formErrors.orgName ? 'border-red-500' : 'border-gray-300'}`} />
                {formErrors.orgName && <p className="text-red-500 text-xs mt-1">{formErrors.orgName}</p>}
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="notes" value={formData.notes} onChange={handleChange}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none h-20 resize-none" />
              </div>
            </div>
          </section>

          {/* SECTION II: Waktu & Tempat */}
          <section>
            <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">II. Waktu & Tempat</h3>
            <div className="bg-gray-50 p-6 rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">

              {/* Kalau BUKAN recurring: tampilkan tanggal biasa */}
              {!isRecurring && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                  <input type="date" name="date" value={formData.date} onChange={handleChange}
                    className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.date ? 'border-red-500' : 'border-gray-300'}`} />
                  {formErrors.date && <p className="text-red-500 text-xs mt-1">{formErrors.date}</p>}
                </div>
              )}

              {/* Kalau recurring: tampilkan hari + rentang tanggal */}
              {isRecurring && (
                <>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Hari *</label>
                    <select name="day_of_week" value={recurringData.day_of_week} onChange={handleRecurringChange}
                      className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                      {DAYS.map((d, i) => <option key={i} value={i}>{d}</option>)}
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Frekuensi</label>
                    <select name="frequency" value={recurringData.frequency} onChange={handleRecurringChange}
                      className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                      <option value="weekly">Mingguan</option>
                      <option value="daily">Harian</option>
                      <option value="monthly">Bulanan</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value={recurringData.start_date} onChange={handleRecurringChange}
                      className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.start_date ? 'border-red-500' : 'border-gray-300'}`} />
                    {formErrors.start_date && <p className="text-red-500 text-xs mt-1">{formErrors.start_date}</p>}
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir *</label>
                    <input type="date" name="end_date" value={recurringData.end_date} onChange={handleRecurringChange}
                      className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.end_date ? 'border-red-500' : 'border-gray-300'}`} />
                    {formErrors.end_date && <p className="text-red-500 text-xs mt-1">{formErrors.end_date}</p>}
                  </div>
                </>
              )}

              {/* Ruangan */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Ruangan *</label>
                <div className="flex gap-2">
                  <select name="room" value={formData.room} onChange={handleChange}
                    className={`flex-1 border rounded-lg px-3 py-2 text-sm bg-white ${formErrors.room ? 'border-red-500' : 'border-gray-300'}`}>
                    <option value="">-- Pilih Ruangan --</option>
                    {roomList.map((r) => (
                      <option key={r.room_id || r.id} value={r.room_name}>{r.room_name}</option>
                    ))}
                  </select>
                  {!isRecurring && (
                    <button onClick={handleCheckAvailability} disabled={loadingCheck}
                      className="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-xs px-4 rounded-lg font-medium transition whitespace-nowrap">
                      {loadingCheck ? '...' : 'Cek'}
                    </button>
                  )}
                </div>
                {formErrors.room && <p className="text-red-500 text-xs mt-1">{formErrors.room}</p>}
                <div className="mt-2 min-h-[20px]">
                  {availabilityStatus === 'available' && <p className="text-xs text-green-600 font-bold flex items-center gap-1"><Icons.Check /> Tersedia!</p>}
                  {availabilityStatus === 'booked'    && <p className="text-xs text-red-600 font-bold flex items-center gap-1"><Icons.X /> Penuh / Booked</p>}
                </div>
              </div>

              {/* Jam */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                <input type="time" name="startTime" value={formData.startTime} onChange={handleChange}
                  className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.startTime ? 'border-red-500' : 'border-gray-300'}`} />
                {formErrors.startTime && <p className="text-red-500 text-xs mt-1">{formErrors.startTime}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                <input type="time" name="endTime" value={formData.endTime} onChange={handleChange}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>
          </section>

          {/* SECTION III: Kontak */}
          <section>
            <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider border-b pb-2 mb-4">III. Kontak</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">PIC *</label>
                <input type="text" name="pic" value={formData.pic} onChange={handleChange}
                  className={`w-full border rounded-lg px-3 py-2 text-sm ${formErrors.pic ? 'border-red-500' : 'border-gray-300'}`} />
                {formErrors.pic && <p className="text-red-500 text-xs mt-1">{formErrors.pic}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <input type="text" name="phone" value={formData.phone} onChange={handleChange}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>
          </section>

          {/* Info recurring preview */}
          {isRecurring && recurringData.start_date && recurringData.end_date && (
            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
              <p className="font-bold mb-1">📅 Preview Recurring</p>
              <p>Sistem akan membuat booking setiap <strong>{DAYS[recurringData.day_of_week]}</strong> dari <strong>{recurringData.start_date}</strong> sampai <strong>{recurringData.end_date}</strong>.</p>
            </div>
          )}

          <div className="pt-6 border-t border-gray-100 flex justify-end gap-4">
            <button
              onClick={() => {
                setFormData({ eventName: '', orgName: '', date: '', room: '', startTime: '', endTime: '', pic: '', phone: '', notes: '' });
                setRecurringData({ day_of_week: 1, start_date: '', end_date: '', frequency: 'weekly' });
                setIsRecurring(false);
              }}
              className="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-bold hover:bg-gray-50 transition"
            >Reset</button>
            <button onClick={handleSubmit}
              className="px-8 py-2.5 rounded-lg bg-[#990000] text-white font-bold hover:bg-[#7a0000] transition flex items-center gap-2">
              {isRecurring && <Icons.Repeat />}
              {isRecurring ? 'Submit Recurring' : 'Submit Pengajuan'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// ===================== STATUS TABLE =====================
const StatusTable = ({ bookings, onBack, onCancel }) => (
  <div className="pb-10">
    <div className="flex items-center justify-between mb-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-800">Status Pengajuan</h2>
        <p className="text-sm text-gray-500">Pantau status persetujuan peminjaman ruangan Anda.</p>
      </div>
      <button onClick={onBack} className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold flex items-center gap-2 transition">
        <Icons.ChevronLeft /> Kembali
      </button>
    </div>
    <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <div className="p-4 border-b bg-gray-50 flex justify-between items-center">
        <p className="text-xs text-gray-500">Total: <span className="font-bold text-gray-900">{bookings.length}</span></p>
      </div>
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead className="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
            <tr>
              <th className="px-6 py-4 border-b">ID</th>
              <th className="px-6 py-4 border-b">Waktu & Ruangan</th>
              <th className="px-6 py-4 border-b">Kegiatan</th>
              <th className="px-6 py-4 border-b">Tipe</th>
              <th className="px-6 py-4 border-b text-center">Status</th>
              <th className="px-6 py-4 border-b text-center">Aksi</th>
            </tr>
          </thead>
          <tbody className="text-sm text-gray-700 divide-y divide-gray-100">
            {bookings.length === 0 ? (
              <tr><td colSpan="6" className="text-center py-16 text-gray-400">Belum ada data pengajuan.</td></tr>
            ) : bookings.map((item) => (
              <tr key={item.id} className="hover:bg-blue-50/50 transition-colors">
                <td className="px-6 py-4 font-mono text-xs text-gray-500">#{item.id}</td>
                <td className="px-6 py-4">
                  <div className="font-bold text-gray-800">{item.date}</div>
                  <div className="text-xs text-gray-500 flex items-center gap-1 mt-1"><Icons.Clock /> {item.time}</div>
                  <span className="inline-block mt-2 bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-bold">{item.room}</span>
                </td>
                <td className="px-6 py-4">
                  <div className="font-bold text-[#990000]">{item.event}</div>
                  <div className="text-xs text-gray-600 mt-0.5">{item.org}</div>
                </td>
                <td className="px-6 py-4">
                  {item.recurring_group_id ? (
                    <span className="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 w-fit">
                      <Icons.Repeat /> Rutin
                    </span>
                  ) : (
                    <span className="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold w-fit">Biasa</span>
                  )}
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase border ${
                    item.status === 'Disetujui' ? 'bg-green-100 text-green-700 border-green-200' :
                    item.status === 'Ditolak'   ? 'bg-red-100 text-red-700 border-red-200' :
                    'bg-yellow-100 text-yellow-700 border-yellow-200'
                  }`}>{item.status}</span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button onClick={() => onCancel(item.id)}
                    className="text-red-500 hover:text-white hover:bg-red-600 border border-red-200 px-3 py-1.5 rounded text-xs font-bold flex items-center gap-1 mx-auto transition">
                    <Icons.Trash /> Batalkan
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  </div>
);

// ===================== PEMINJAMAN PAGE =====================
const PeminjamanPage = ({ user, onBackToMenu, onToDashboard }) => {
  const [activeTab, setActiveTab]         = useState('pengajuan');
  const [bookings, setBookings]           = useState([]);
  const [isSubmitting, setIsSubmitting]   = useState(false);
  const [showSuccessModal, setShowSuccessModal] = useState(false);
  const [successMsg, setSuccessMsg]       = useState('');

  const fetchBookings = async () => {
    try {
      const userId = user?.id || user?.user_id || 0;
      const res = await fetch(`https://project-kelompok-5-production.up.railway.app/api/booking/list.php?user_id=${userId}`);
      const data = await res.json();
      if (Array.isArray(data)) setBookings(data);
    } catch (e) { console.error(e); }
  };

  useEffect(() => { fetchBookings(); }, []);

  const handleSubmit = async (formData) => {
    setIsSubmitting(true);
    const payload = {
      user_id: user?.id || user?.user_id || 1,
      event_name: formData.eventName,
      organization: formData.orgName,
      pic: formData.pic,
      phone: formData.phone,
      event_description: formData.notes || "",
      start_datetime: `${formData.date} ${formData.startTime}`,
      end_datetime: `${formData.date} ${formData.endTime || formData.startTime}`,
      rooms: [formData.room],
      inventory: []
    };
    try {
      const data = await createBooking(payload);
      if (data.status === "success") {
        setSuccessMsg("Data telah disubmit. Silakan pantau status di tabel ini.");
        setShowSuccessModal(true);
        fetchBookings();
      } else {
        alert("Gagal: " + (data.message || "Unknown error"));
      }
    } catch (e) {
      alert("Koneksi Gagal.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleSubmitRecurring = async (payload) => {
    setIsSubmitting(true);
    try {
      const data = await createRecurringBooking(payload);
      if (data.status) {
        setSuccessMsg(`${data.total_sessions} sesi berhasil dibuat untuk tanggal: ${data.dates.join(', ')}`);
        setShowSuccessModal(true);
        fetchBookings();
      } else {
        if (data.conflicts) {
          alert("Konflik jadwal pada tanggal: " + data.conflicts.join(', '));
        } else {
          alert("Gagal: " + (data.message || "Unknown error"));
        }
      }
    } catch (e) {
      alert("Koneksi Gagal.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleCancelBooking = async (id) => {
    if (!id) return alert("ID tidak valid.");
    if (!window.confirm(`Yakin ingin menghapus booking #${id}?`)) return;
    try {
      const res  = await fetch("https://project-kelompok-5-production.up.railway.app/api/booking/delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      });
      const data = await res.json();
      if (data.status === "success") { fetchBookings(); }
      else alert("Gagal: " + data.message);
    } catch (e) { alert("Gagal terhubung."); }
  };

  return (
    <div className="flex h-screen bg-gray-50 overflow-hidden font-sans">
      <aside className="w-64 bg-[#7a1e1e] text-white flex flex-col shrink-0 shadow-2xl z-20">
        <div className="p-6 border-b border-red-900/50 flex items-center gap-3 bg-[#601010]">
          <div className="bg-white p-1.5 rounded shadow-sm">
            <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="w-8 h-auto" />
          </div>
          <div><h1 className="font-bold text-sm tracking-wide">Biro Kemahasiswaan</h1><p className="text-[10px] text-red-200">Universitas Bakrie</p></div>
        </div>
        <nav className="flex-1 overflow-y-auto py-6 space-y-1">
          <div className="px-6 text-[10px] font-bold text-red-300 uppercase tracking-widest mb-2">Main Menu</div>
          <button onClick={() => setActiveTab('pengajuan')}
            className={`w-full text-left px-6 py-3 flex items-center gap-3 transition-all border-l-4 ${activeTab === 'pengajuan' ? 'bg-[#990000] border-white' : 'border-transparent text-red-100 hover:bg-[#852020]'}`}>
            <Icons.Document /><span className="font-medium text-sm">Form Pengajuan</span>
          </button>
          <button onClick={() => setActiveTab('status')}
            className={`w-full text-left px-6 py-3 flex items-center gap-3 transition-all border-l-4 ${activeTab === 'status' ? 'bg-[#990000] border-white' : 'border-transparent text-red-100 hover:bg-[#852020]'}`}>
            <Icons.Search /><span className="font-medium text-sm">Status Pengajuan</span>
          </button>
          <div className="mt-8 px-6 text-[10px] font-bold text-red-300 uppercase tracking-widest mb-2">System</div>
          <button onClick={onBackToMenu}
            className="w-full text-left px-6 py-3 flex items-center gap-3 text-red-100 hover:bg-[#852020] border-l-4 border-transparent">
            <Icons.ChevronLeft /><span className="font-medium text-sm">Kembali ke Menu</span>
          </button>
        </nav>
      </aside>

      <main className="flex-1 flex flex-col h-full overflow-hidden">
        <header className="h-16 bg-white shadow-sm border-b flex items-center justify-between px-8 z-10 shrink-0">
          <div className="text-sm text-gray-500">
            <span className="cursor-pointer hover:text-[#990000]" onClick={onToDashboard}>Home</span>
            <span className="mx-2">/</span>
            <span className="font-bold text-[#990000]">Peminjaman Ruangan</span>
          </div>
          <div className="flex items-center gap-4">
            <div className="text-right hidden md:block">
              <p className="text-sm font-bold text-gray-800">Hi, {user?.name || "User"}</p>
              <p className="text-xs text-gray-500 capitalize">{user?.role || "Mahasiswa"}</p>
            </div>
            <div className="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-bold">
              {user?.name?.charAt(0).toUpperCase() || "U"}
            </div>
          </div>
        </header>

        <div className="flex-1 overflow-y-auto p-8">
          {activeTab === "pengajuan" ? (
            <FormPengajuan user={user} onSubmitData={handleSubmit} onSubmitRecurring={handleSubmitRecurring} onBack={onBackToMenu} />
          ) : (
            <StatusTable bookings={bookings} onBack={() => setActiveTab("pengajuan")} onCancel={handleCancelBooking} />
          )}
        </div>
      </main>

      {isSubmitting && <LoadingOverlay message="Mengirim Data..." />}
      {showSuccessModal && (
        <ModalSuccess
          title="Berhasil!"
          message={successMsg}
          onClose={() => { setShowSuccessModal(false); setActiveTab('status'); }}
        />
      )}
    </div>
  );
};

// ===================== BIMA PAGE =====================
const BimaPage = ({ user, onBack, onNavigate }) => {
  const menus = [
    { label: "Pendanaan Kompetisi",        disabled: true },
    { label: "Asuransi Mahasiswa",          disabled: true },
    { label: "Beasiswa & Bantuan",          disabled: true },
    { label: "Layanan Psikologi",           disabled: true },
    { label: "Student Exit Letter",         disabled: true },
    { label: "Peminjaman Fasilitas Kampus", action: 'peminjaman', highlight: true },
    { label: "Analytics Ruangan",           action: 'analytics',  highlight: false, special: true },
    { label: "Buku Panduan",               disabled: true },
    { label: "Surat Keterangan Aktif",     disabled: true },
  ];

  return (
    <div className="min-h-screen bg-white font-sans flex flex-col">
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

      <div className="flex-1 max-w-7xl mx-auto w-full p-8 md:p-12">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-extrabold text-gray-900 mb-3">Layanan Kemahasiswaan</h2>
          <p className="text-gray-500">Pilih layanan yang Anda butuhkan.</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {menus.map((item, idx) => (
            <div key={idx}
              onClick={() => item.action ? onNavigate(item.action) : alert("Fitur ini akan segera hadir.")}
              className={`relative h-40 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all cursor-pointer flex flex-col items-center justify-center p-6 text-center border
                ${item.highlight ? 'bg-[#1a1a1a] border-black' : item.special ? 'bg-blue-700 border-blue-800' : 'bg-white border-gray-100 hover:border-gray-300'}`}>
              <h3 className={`font-bold text-lg ${item.highlight || item.special ? 'text-white' : 'text-gray-800'}`}>{item.label}</h3>
              {item.disabled && <span className="absolute top-3 right-3 text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded">Soon</span>}
              {item.special && <span className="mt-2 text-xs text-blue-200 flex items-center gap-1"><Icons.Chart /> Lihat statistik ruangan</span>}
            </div>
          ))}
        </div>
      </div>

      <footer className="bg-[#1a1a1a] text-white py-8 border-t border-gray-800">
        <div className="max-w-7xl mx-auto px-8 flex justify-between items-center">
          <p className="text-xs text-gray-500">&copy; 2025 Universitas Bakrie</p>
        </div>
      </footer>
    </div>
  );
};

// ===================== DASHBOARD =====================
const Dashboard = ({ user, onLogout, onNavigate }) => {
  const modules = [
    { title: "BIG",                   color: "bg-[#c0392b]" },
    { title: "E-Learning",            color: "bg-[#2980b9]" },
    { title: "Parent Portal",         color: "bg-[#d35400]" },
    { title: "Perpustakaan",          color: "bg-[#27ae60]" },
    { title: "Info PMB",              color: "bg-[#f39c12]" },
    { title: "BIMA (Kemahasiswaan)",  color: "bg-[#800000]", action: 'bima' },
  ];

  return (
    <div className="min-h-screen bg-gray-100 font-sans flex items-center justify-center p-4 md:p-8">
      <div className="w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col min-h-[80vh]">
        <div className="bg-[#990000] text-white p-6 flex justify-between items-center shadow-lg">
          <div className="flex items-center gap-5">
            <div className="bg-white p-2 rounded-lg shadow-md">
              <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo UB" className="h-10 w-auto" />
            </div>
            <div><h1 className="font-extrabold text-2xl tracking-wide uppercase">BIG 2.0</h1><p className="text-xs text-red-200 tracking-wider">Bakrie Information Gateway</p></div>
          </div>
          <div className="flex items-center gap-4">
            <div className="hidden md:block text-right">
              <p className="text-sm font-bold">Halo, {user?.name}</p>
            </div>
            <button onClick={onLogout} className="bg-white/10 hover:bg-white hover:text-[#990000] text-white border border-white/30 px-5 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
              <Icons.Logout /> Logout
            </button>
          </div>
        </div>
        <div className="flex-1 p-8 md:p-12 bg-gray-50">
          <div className="mb-8 border-b border-gray-200 pb-4">
            <h2 className="text-2xl font-bold text-gray-800">Daftar Modul Aplikasi</h2>
            <p className="text-gray-500 mt-1">Silakan pilih modul yang ingin Anda akses.</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {modules.map((modul, idx) => (
              <div key={idx}
                onClick={() => modul.action ? onNavigate(modul.action) : alert("Maintenance.")}
                className={`${modul.color} group relative h-48 rounded-xl shadow-lg cursor-pointer transform hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between p-6`}>
                <div className="relative z-10">
                  <h3 className="text-white text-xl font-bold">{modul.title}</h3>
                </div>
              </div>
            ))}
          </div>
        </div>
        <div className="bg-white p-4 text-center text-xs text-gray-400 border-t">&copy; 2025 Universitas Bakrie</div>
      </div>
    </div>
  );
};

// ===================== LOGIN PAGE =====================
const LoginPage = ({ onLogin, loading }) => {
  const [user, setUser] = useState('');
  const [pass, setPass] = useState('');
  const handleSubmit = (e) => { e.preventDefault(); onLogin(user, pass); };

  return (
    <div className="min-h-screen flex bg-white font-sans">
      <div className="hidden md:flex md:w-[60%] relative bg-slate-900 flex-col justify-between text-white overflow-hidden">
        <div className="absolute inset-0"><div className="absolute inset-0 bg-gradient-to-br from-[#990000] via-[#5e0d0d] to-black opacity-90"></div></div>
        <div className="relative z-10 p-16 flex flex-col h-full justify-between">
          <div>
            <div className="flex items-center gap-3 mb-8">
              <div className="w-12 h-12 bg-white rounded-lg p-2 flex items-center justify-center shadow-lg">
                <img src={LOGO_PRIMARY_URL} onError={handleLogoError} alt="Logo" className="w-full h-auto" />
              </div>
              <h1 className="text-xl font-bold tracking-widest uppercase">Universitas Bakrie</h1>
            </div>
            <h2 className="text-5xl font-extrabold leading-tight mb-6">UBakrie<br/>Space</h2>
            <p className="text-lg text-red-100 max-w-md font-light">Sistem peminjaman ruangan digital Universitas Bakrie.</p>
          </div>
        </div>
      </div>
      <div className="flex-1 flex flex-col items-center justify-center p-8 md:p-12">
        <div className="w-full max-w-md">
          <div className="text-center mb-10">
            <h2 className="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
            <p className="text-gray-500">Masuk ke akun BIG 2.0 Anda</p>
          </div>
          <form onSubmit={handleSubmit} className="space-y-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 mb-2">Email</label>
              <input type="text" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#990000] outline-none" placeholder="email@student.bakrie.ac.id" value={user} onChange={(e) => setUser(e.target.value)} />
            </div>
            <div>
              <label className="block text-sm font-bold text-gray-700 mb-2">Password</label>
              <input type="password" required className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#990000] outline-none" placeholder="••••••••" value={pass} onChange={(e) => setPass(e.target.value)} />
            </div>
            <button type="submit" disabled={loading}
              className="w-full bg-[#990000] hover:bg-[#7a0000] text-white font-bold py-3.5 rounded-lg shadow-lg transition">
              {loading ? "Memproses..." : "MASUK APLIKASI"}
            </button>
          </form>
          <div className="mt-10 text-center text-xs text-gray-400"><p>&copy; 2025 Universitas Bakrie.</p></div>
        </div>
      </div>
    </div>
  );
};

// ===================== APP =====================
function App() {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem("user");
    return saved ? JSON.parse(saved) : null;
  });
  const [loading, setLoading]           = useState(false);
  const [currentPage, setCurrentPage]   = useState('dashboard');

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
    } catch {
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
  if (currentPage === 'analytics')  return <AnalyticsPage  user={user} onBack={() => setCurrentPage('bima')} />;
  if (currentPage === 'peminjaman') return <PeminjamanPage user={user} onBackToMenu={() => setCurrentPage('bima')} onToDashboard={() => setCurrentPage('dashboard')} />;
  if (currentPage === 'bima')       return <BimaPage       user={user} onBack={() => setCurrentPage('dashboard')} onNavigate={setCurrentPage} />;
  return <Dashboard user={user} onLogout={handleLogout} onNavigate={setCurrentPage} />;
}

export default App;