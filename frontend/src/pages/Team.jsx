import { useEffect, useState } from 'react'
import api from '../api'
const RC = { admin:'bg-purple-100 text-purple-700', agent:'bg-sky-100 text-sky-700', customer:'bg-slate-100 text-slate-600' }
export default function Team() {
  const [users, setUsers] = useState([])
  const [form, setForm] = useState({ name:'', email:'', password:'', role:'agent' })
  const [err, setErr] = useState('')
  const load = () => api.get('/users').then(r => setUsers(r.data)).catch(()=>setErr('Admins only'))
  useEffect(() => { load() }, [])
  const set = k => e => setForm({ ...form, [k]: e.target.value })
  const add = async (e) => {
    e.preventDefault(); setErr('')
    try { await api.post('/users', form); setForm({ name:'',email:'',password:'',role:'agent' }); load() }
    catch (e2) { setErr(e2?.response?.data?.message || 'Failed') }
  }
  return (
    <div className="space-y-6">
      <h1 className="text-xl font-bold">Team — <span className="text-slate-500 text-base font-normal">admin only</span></h1>
      {err && <div className="bg-red-50 text-red-600 text-sm p-2 rounded">{err}</div>}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-500 text-left"><tr><th className="p-3">Name</th><th>Email</th><th>Role</th></tr></thead>
          <tbody>{users.map(u => (
            <tr key={u.id} className="border-t"><td className="p-3">{u.name}</td><td>{u.email}</td>
              <td><span className={`px-2 py-0.5 rounded text-xs ${RC[u.role]}`}>{u.role}</span></td></tr>
          ))}</tbody>
        </table>
      </div>
      <form onSubmit={add} className="bg-white rounded-lg shadow p-4 space-y-3 max-w-md">
        <h2 className="font-semibold">Add team member</h2>
        <input className="w-full border rounded px-3 py-2" placeholder="Name" value={form.name} onChange={set('name')} />
        <input className="w-full border rounded px-3 py-2" placeholder="Email" value={form.email} onChange={set('email')} />
        <input className="w-full border rounded px-3 py-2" type="password" placeholder="Password (min 6)" value={form.password} onChange={set('password')} />
        <select className="w-full border rounded px-3 py-2" value={form.role} onChange={set('role')}><option value="agent">agent</option><option value="customer">customer</option></select>
        <button className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Create user</button>
      </form>
    </div>
  )
}
