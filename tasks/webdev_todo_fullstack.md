# Full‑Stack ToDo‑App (Konzept)

Dieses Dokument enthält ein grundlegendes Konzept für eine Full‑Stack
ToDo‑Anwendung. Eine solche Anwendung besteht aus zwei Hauptkomponenten:

1. **Frontend (Client):**  Verantwortlich für die Benutzeroberfläche. Sie
   ermöglicht das Erstellen, Anzeigen, Aktualisieren und Löschen von ToDo‑
   Einträgen.
2. **Backend (Server):**  Stellt eine API bereit, über die der Client
   Daten abrufen und speichern kann. Das Backend verwaltet die Datenbank,
   authentifiziert Benutzer und implementiert die Geschäftslogik.

## Frontend

- Verwende ein modernes Framework wie React, Vue oder Angular. Eine
  Minimalversion kann auch mit Vanilla JavaScript erstellt werden.
- Komponenten:
  - Formular zum Erstellen neuer Aufgaben (mit Titel und Beschreibung).
  - Liste der Aufgaben mit Buttons für „Erledigt“ und „Löschen“.
- Nutze `fetch()` oder `axios`, um HTTP‑Anfragen an die Backend‑API zu
  senden.

## Backend

- Implementiere einen Server in Node.js (Express), Python (Flask/Django) oder
  einer anderen Sprache. Beispiel für Express:

```js
const express = require('express');
const app = express();
app.use(express.json());

let todos = [];
app.get('/api/todos', (req, res) => res.json(todos));
app.post('/api/todos', (req, res) => {
  const todo = { id: Date.now(), ...req.body };
  todos.push(todo);
  res.status(201).json(todo);
});
// Implementiere PUT zum Aktualisieren und DELETE zum Löschen
app.listen(3000);
```

- Optional: Datenbankanbindung (SQLite, MySQL, MongoDB) statt in‑Memory.
- Implementiere Authentifizierung mit JSON Web Tokens (JWT), um Benutzern zu
  erlauben, nur ihre eigenen Aufgaben zu sehen und zu bearbeiten.

## Tipps

- Beginne mit einem kleinen Funktionsumfang und erweitere die Anwendung
  schrittweise.
- Verwende Tools wie Postman, um die API während der Entwicklung zu testen.
- Achte auf sichere Passwortspeicherung (Hashing) und valide Eingaben.

## Lösung

Da diese Aufgabe einen Full‑Stack‑Ansatz erfordert, ist kein vollständiger
Quellcode im Portfolio enthalten. Stattdessen dient diese Beschreibung als
Leitfaden. Du kannst das Beispiel oben als Grundlage verwenden und die
ToDo‑App selbständig implementieren.
