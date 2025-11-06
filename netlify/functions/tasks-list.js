import { sqlClient } from '../db.js'
export const handler = async () => {
  const sql = sqlClient();
  const rows = await sql`
    select t.id, t.area_id, a.name as area_name, t.title, t.hint, t.description
    from tasks t left join areas a on a.id=t.area_id
    order by t.id desc limit 100`;
  return {
    statusCode: 200,
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify({ ok: true, rows })
  }
}