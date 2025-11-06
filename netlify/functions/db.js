import { neon } from '@netlify/neon';
export function sqlClient() {
  const sql = neon(); // uses NETLIFY_DATABASE_URL
  return sql;
}