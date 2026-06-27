import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../api'
const SC = { open:'bg-green-100 text-green-700', pending:'bg-yellow-100 text-yellow-700', resolved:'bg-blue-100 text-blue-700', closed:'bg-slate-200 text-slate-600' }
const PC = { low:'bg-slate-100 text-slate-600', medium:'bg-sky-100 text-sky-700', high:'bg-orange-100 text-orange-700', urgent:'bg-red-100 text-red-700' }
export default function Tickets() {
  const [tickets, setTickets] = useState([])
  const [metrics, setMetrics] = useState(null)
  const [f, setF] = useState({ status:'', priority:'', q:'' })
  const load = () => {
    const p = new URLSearchParams(Object.entries(f).filter(([,v])=>v))
    api.get('/tickets?'+p).then(r => setTickets(r.data.data))
  }
  useEffect(() => { api.get('/dashboard/metrics').then(r=>setMetrics(r.data)) }, [])
  useEffect(load, [f])
  return (
    <div className="space-y-6">
      {metrics && <div className="grid grid-cols-4 gap-4">
        {[['Total',metrics.total],['Open',metrics.open],['Unassigned',metrics.unassigned],['Urgent',metrics.by_priority?.urgent||0]].map(([k,v])=>(
          <div key={k} className="bg-white rounded-lg shadow p-4"><div className="text-3xl font-bold text-indigo-600">{v}</div><div className="text-sm text-slate-500">{k}</div></div>
        ))}
      </div>}
      <div className="flex gap-3 flex-wrap">
        <input className="border rounded px-3 py-1.5" placeholder="Search subject/body…" value={f.q} onChange={e=>setF({...f,q:e.target.value})} />
        <select className="border rounded px-2 py-1.5" value={f.status} onChange={e=>setF({...f,status:e.target.value})}><option value="">All status</option>{['open','pending','resolved','closed'].map(s=><option key={s}>{s}</option>)}</select>
        <select className="border rounded px-2 py-1.5" value={f.priority} onChange={e=>setF({...f,priority:e.target.value})}><option value="">All priority</option>{['low','medium','high','urgent'].map(s=><option key={s}>{s}</option>)}</select>
      </div>
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-500 text-left"><tr><th className="p-3">Subject</th><th>Status</th><th>Priority</th><th>Requester</th><th>Assignee</th></tr></thead>
          <tbody>
            {tickets.map(t => (
              <tr key={t.id} className="border-t hover:bg-slate-50">
                <td className="p-3"><Link to={`/tickets/${t.id}`} className="text-indigo-600 font-medium">{t.subject}</Link></td>
                <td><span className={`px-2 py-0.5 rounded text-xs ${SC[t.status]}`}>{t.status}</span></td>
                <td><span className={`px-2 py-0.5 rounded text-xs ${PC[t.priority]}`}>{t.priority}</span></td>
                <td>{t.requester?.name}</td>
                <td>{t.assignee?.name || <span className="text-slate-400">—</span>}</td>
              </tr>
            ))}
            {!tickets.length && <tr><td colSpan="5" className="p-6 text-center text-slate-400">No tickets</td></tr>}
          </tbody>
        </table>
      </div>
    </div>
  )
}
