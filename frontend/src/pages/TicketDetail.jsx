import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import api from '../api'
export default function TicketDetail() {
  const { id } = useParams()
  const [t, setT] = useState(null)
  const [body, setBody] = useState('')
  const [internal, setInternal] = useState(false)
  const load = () => api.get(`/tickets/${id}`).then(r => setT(r.data))
  useEffect(() => { load() }, [id])
  const reply = async () => { if(!body) return; await api.post(`/tickets/${id}/replies`, { body, is_internal: internal }); setBody(''); setInternal(false); load() }
  const setStatus = async (status) => { await api.patch(`/tickets/${id}`, { status }); load() }
  if (!t) return <div>Loading…</div>
  return (
    <div className="space-y-4">
      <Link to="/" className="text-indigo-600 text-sm">← Back to board</Link>
      <div className="bg-white rounded-lg shadow p-5">
        <div className="flex justify-between items-start">
          <div><h1 className="text-xl font-bold">{t.subject}</h1><p className="text-slate-600 mt-1">{t.description}</p></div>
          <select className="border rounded px-2 py-1" value={t.status} onChange={e=>setStatus(e.target.value)}>{['open','pending','resolved','closed'].map(s=><option key={s}>{s}</option>)}</select>
        </div>
        <div className="text-xs text-slate-500 mt-3">Priority: {t.priority} · Requester: {t.requester?.name} · Assignee: {t.assignee?.name||'—'}</div>
      </div>
      <div className="bg-white rounded-lg shadow p-5 space-y-3">
        <h2 className="font-semibold">Conversation</h2>
        {t.replies?.map(r => (
          <div key={r.id} className={`p-3 rounded ${r.is_internal ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50'}`}>
            <div className="text-xs text-slate-500 mb-1">{r.user?.name} {r.is_internal && <span className="text-amber-600 font-medium">· internal note</span>}</div>
            <div>{r.body}</div>
          </div>
        ))}
        {!t.replies?.length && <p className="text-slate-400 text-sm">No replies yet.</p>}
        <div className="border-t pt-3 space-y-2">
          <textarea className="w-full border rounded p-2" rows="2" placeholder="Write a reply…" value={body} onChange={e=>setBody(e.target.value)} />
          <div className="flex items-center justify-between">
            <label className="text-sm flex items-center gap-2"><input type="checkbox" checked={internal} onChange={e=>setInternal(e.target.checked)} /> Internal note (agents only)</label>
            <button onClick={reply} className="bg-indigo-600 text-white px-4 py-1.5 rounded hover:bg-indigo-700">Send</button>
          </div>
        </div>
      </div>
    </div>
  )
}
