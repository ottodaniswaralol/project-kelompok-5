import React from 'react';
import { login } from "../services/api";

const handleLogin = async () => {
  const res = await login(username, password);

  if (res.status) {
    localStorage.setItem("user", JSON.stringify(res.user));
    navigate("/");
  } else {
    alert(res.message);
  }
};

const Login = () => {
  return (
    <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
      <div className="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border-t-8 border-red-700">
        <div className="text-center mb-10">
          <h1 className="text-3xl font-black text-gray-800 tracking-tight">UNIVERSITAS BAKRIE</h1>
          <p className="text-red-700 font-bold text-xs uppercase tracking-widest mt-2">Smart Room Booking System</p>
        </div>

        <form className="space-y-6">
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2 ml-1">Email Campus</label>
            <input 
              type="email" 
              placeholder="yohanes@bakrie.ac.id"
              className="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-red-100 focus:border-red-600 outline-none transition-all"
            />
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2 ml-1">Password</label>
            <input 
              type="password" 
              placeholder="••••••••"
              className="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-red-100 focus:border-red-600 outline-none transition-all"
            />
          </div>

          <button className="w-full bg-red-700 hover:bg-red-800 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-red-200 transition-all active:scale-95">
            LOG IN
          </button>
        </form>

        <div className="mt-10 pt-6 border-t border-gray-100 text-center">
          <p className="text-[10px] text-gray-400 font-medium">KELOMPOK 5 PROJECT UAS • 2025</p>
        </div>
      </div>
    </div>
  );
};

export default Login;