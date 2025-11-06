/*
 * GitHub Profile Search
 *
 * Dieses Beispiel demonstriert, wie man mit der Fetch API Daten von einer
 * externen Quelle (GitHub REST API) abruft. In einer echten Anwendung
 * rufst du z. B. https://api.github.com/users/<username> auf und
 * verarbeitest die JSON-Antwort, um Benutzerinformationen wie Name,
 * Profilbild und Repos anzuzeigen. Der folgende Code enthält die
 * Grundstruktur ohne UI-Elemente; integriere ihn in eine HTML-Datei mit
 * einem Eingabefeld und einer Ergebnisanzeige.
 */

async function fetchGitHubProfile(username) {
  try {
    const response = await fetch(`https://api.github.com/users/${username}`);
    if (!response.ok) {
      throw new Error('Benutzer nicht gefunden');
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Fehler beim Abrufen des Profils:', error);
    throw error;
  }
}

// Beispielnutzung:
// fetchGitHubProfile('octocat').then(profile => console.log(profile));
