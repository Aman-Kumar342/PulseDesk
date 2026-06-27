import { Routes, Route, Navigate, Link, useNavigate } from 'react-router-dom'
import { useEffect, useState } from 'react'
import api from './api'
import Login from './pages/Login'
import Register from './pages/Register'
import Tickets from './pages/Tickets'
import TicketDetail from './pages/TicketDetail'

function Shell({ children }) {
  const nav = useNavigate()
  const [me, setMe] = useState(null)
  useEffect(() => { api.get('/me').then(r => setMe(r.data)).catch(() => nav('/login')) }, [])
  return (
    <div className="min-h-screen bg-slate-50 text-slate-800">
      <header className="bg-indigo-600 text-white px-6 py-3 flex items-center justify-between shadow">
        <Link to="/" className="font-bold text-lg">🎫 PulseDesk</Link>
        <div className="text-sm flex items-center gap-4">
          {me && <span>{me.name} · <span className="opacity-80">{me.role}</span> · {me.organization?.name}</span>}
          <button onClick={() => { localStorage.removeItem('token'); nav('/login') }} className="bg-indigo-500 hover:bg-indigo-400 px-3 py-1 rounded">Logout</button>
        </div>
      </header>
      <main className="max-w-6xl mx-auto p-6">{children}</main>
    </div>
  )
}
const auth = () => !!localStorage.getItem('token')
export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/" element={auth() ? <Shell><Tickets /></Shell> : <Navigate to="/login" />} />
      <Route path="/tickets/:id" element={auth() ? <Shell><TicketDetail /></Shell> : <Navigate to="/login" />} />
    </Routes>
  )
}
