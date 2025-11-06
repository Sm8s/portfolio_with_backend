import { sqlClient } from '../db.js'
export const handler = async (event) => {
  if (event.httpMethod !== 'POST') return { statusCode: 405, body: 'Method Not Allowed' }
  const { id, slug, title, content } = JSON.parse(event.body || '{}')
  if (!slug || !title) return { statusCode: 400, body: 'slug and title required' }
  const sql = sqlClient();
  if (id) {
    await sql`update pages set slug=${slug}, title=${title}, content=${content}, updated_at=now() where id=${id}`;
    return { statusCode: 200, body: JSON.stringify({ ok: true, id }) }
  } else {
    const inserted = await sql`insert into pages (slug, title, content) values (${slug}, ${title}, ${content}) returning id`;
    return { statusCode: 200, body: JSON.stringify({ ok: true, id: inserted[0].id }) }
  }
}