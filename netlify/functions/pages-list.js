import { sqlClient } from '../db.js'
export const handler = async () => {
  const sql = sqlClient();
  const rows = await sql`select id, slug, title, content, updated_at from pages where deleted_at is null order by updated_at desc limit 50`;
  return {
    statusCode: 200,
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify({ ok: true, rows })
  }
}