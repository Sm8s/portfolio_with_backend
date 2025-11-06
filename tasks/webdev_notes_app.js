/*
 * Notes App with localStorage
 *
 * Dieses Skript implementiert eine einfache Notiz-App, die Notizen im
 * lokalen Browser-Speicher (localStorage) speichert. Integriere dieses
 * Skript in eine HTML-Datei mit einem Formular für die Eingabe neuer Notizen
 * und einer Liste zur Anzeige aller gespeicherten Notizen. Beim Laden der
 * Seite werden vorhandene Notizen aus localStorage gelesen und angezeigt.
 */

// Lade vorhandene Notizen aus localStorage oder initialisiere mit leerem Array
function loadNotes() {
  const notesJson = localStorage.getItem('notes');
  return notesJson ? JSON.parse(notesJson) : [];
}

// Speichere ein Array von Notizen in localStorage
function saveNotes(notes) {
  localStorage.setItem('notes', JSON.stringify(notes));
}

// Füge eine neue Notiz hinzu
function addNote(title, content) {
  const notes = loadNotes();
  notes.push({ title, content, date: new Date().toISOString() });
  saveNotes(notes);
}

// Entferne eine Notiz anhand ihres Index
function deleteNote(index) {
  const notes = loadNotes();
  notes.splice(index, 1);
  saveNotes(notes);
}

// Beispiel: Füge eine Notiz hinzu und lade danach alle Notizen
// addNote('Meine erste Notiz', 'Dies ist der Inhalt');
// console.log(loadNotes());
