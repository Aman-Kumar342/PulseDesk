import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import api from '../api'
export default function Register() {
  const [form, setForm] = useState({ organization_name:'', name:'', email:'', password:'' })
  const [err, setErr] = useState('')
  const nav = useNavigate()
  const set = k => e => setForm({ ...form, [k]: e.target.value })
  const submit = async (e) => {
    e.preventDefault(); setErr('')
    try {
      const { data } = await api.post('/register', form)
      localStorage.setItem('token', data.token); nav('/')
    } catch (e2) { setErr(e2?.response?.data?.message || 'Registration failed') }
  }
  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-100">
      <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-md w-96 space-y-3">
        <h1 className="text-2xl font-bold text-indigo-600">🎫 PulseDesk</h1>
        <p className="text-sm text-slate-500">Create your organization</p>
        {err && <div className="bg-red-50 text-red-600 text-sm p-2 rounded">{err}</div>}
        <input className="w-full border rounded px-3 py-2" placeholder="Organization name" value={form.organization_name} onChange={set('organization_name')} />
        <input className="w-full border rounded px-3 py-2" placeholder="Your name" value={form.name} onChange={set('name')} />
        <input className="w-full border rounded px-3 py-2" placeholder="Email" value={form.email} onChange={set('email')} />
        <input className="w-full border rounded px-3 py-2" type="password" placeholder="Password (min 6)" value={form.password} onChange={set('password')} />
        <button className="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Create account</button>
        <p className="text-xs text-slate-500 text-center">Already have an account? <Link to="/login" className="text-indigo-600">Sign in</Link></p>
      </form>
    </div>
  )
}
