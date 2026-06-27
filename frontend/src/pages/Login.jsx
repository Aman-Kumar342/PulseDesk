import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api'
export default function Login() {
  const [email, setEmail] = useState('admin@pulsedesk.test')
  const [password, setPassword] = useState('password')
  const [err, setErr] = useState('')
  const nav = useNavigate()
  const submit = async (e) => {
    e.preventDefault(); setErr('')
    try {
      const { data } = await api.post('/login', { email, password })
      localStorage.setItem('token', data.token); nav('/')
    } catch { setErr('Invalid credentials') }
  }
  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-100">
      <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-md w-96 space-y-4">
        <h1 className="text-2xl font-bold text-indigo-600">🎫 PulseDesk</h1>
        <p className="text-sm text-slate-500">Multi-tenant support desk — sign in</p>
        {err && <div className="bg-red-50 text-red-600 text-sm p-2 rounded">{err}</div>}
        <input className="w-full border rounded px-3 py-2" value={email} onChange={e=>setEmail(e.target.value)} placeholder="Email" />
        <input className="w-full border rounded px-3 py-2" type="password" value={password} onChange={e=>setPassword(e.target.value)} placeholder="Password" />
        <button className="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Sign in</button>
        <p className="text-xs text-slate-400">Demo: admin@pulsedesk.test / password</p>
      </form>
    </div>
  )
}
