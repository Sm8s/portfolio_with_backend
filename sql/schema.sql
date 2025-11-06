-- Datenbankschema für das C‑Portfolio
-- Führe diese Datei in phpMyAdmin oder über die MySQL-Konsole aus, um
-- die notwendigen Tabellen und Beispieldaten anzulegen.

CREATE DATABASE IF NOT EXISTS c_portfolio DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE c_portfolio;

-- Benutzer
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- Aufgaben
CREATE TABLE IF NOT EXISTS areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  strengths TEXT,
  weaknesses TEXT,
  -- Eine ausführliche Beschreibung des Themengebiets. Diese Spalte
  -- liefert Hintergrundwissen und Lerninhalte, die in den
  -- Bereichsseiten angezeigt werden.
  description TEXT
);

CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  area_id INT NOT NULL,
  difficulty INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  hint TEXT,
  solution_file VARCHAR(255),
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_area FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE CASCADE
);

-- Fortschritt
CREATE TABLE IF NOT EXISTS progress (
  user_id INT NOT NULL,
  task_id INT NOT NULL,
  solved TINYINT(1) NOT NULL DEFAULT 0,
  -- Der vom Benutzer eingereichte Code (optional). Wird fuer den Vergleich verwendet.
  user_code TEXT NULL,
  PRIMARY KEY (user_id, task_id),
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);

-- Beispiel-Daten fuer Bereiche und Aufgaben
-- Fuege zunaechst Bereiche (Areas) ein
INSERT INTO areas (name, strengths, weaknesses, description) VALUES
  (
    'C Programmierung',
    'Direkter Zugang zu Speicher und hohe Leistung, verwendet in vielen Systemen und eingebetteten Anwendungen.',
    'Hohe Lernkurve, niedriger Abstraktionsgrad, schwierigeres Fehler‑Management.',
    'Die Programmiersprache C ist eine der grundlegenden und leistungsfähigsten Sprachen der Informatik. Sie zeichnet sich durch effiziente Speicherverwaltung und direkte Kontrolle über Hardware aus. In diesem Bereich lernst du grundlegende Konzepte wie Datentypen, Operatoren, Kontrollstrukturen, Funktionen, Arrays, Zeiger und dynamische Speicherverwaltung. Das Verständnis von C bildet die Grundlage für viele andere Sprachen wie C++, Java und Go. Die Lernkurve ist zwar steil, aber das Wissen über die interne Funktionsweise des Computers belohnt den Aufwand.'
  ),
  (
    'Game Development',
    'Kreative, interaktive Inhalte; grosse Community und viele Ressourcen; spass beim Entwickeln kleiner Projekte.',
    'Komplexe Mathematik und Physik; hoher Zeitaufwand für Grafiken und Engine‑Arbeit; harte Konkurrenz.',
    'Spieleentwicklung kombiniert kreative Ideen mit technischer Umsetzung. Ein Spiel besteht aus mehreren Komponenten: das Spielkonzept und die Geschichte (Game Design), die visuellen Assets, Musik und Soundeffekte (Art), die Programmierung und Skripte für Spiellogik sowie die Benutzeroberfläche mit Rückmeldungen an den Spieler. Um moderne Spiele zu erschaffen, musst du Programmiersprachen beherrschen, dich mit Spiele‑Engines auskennen und Konzepte der Benutzeroberflächen‑ und Interaktionsgestaltung verstehen【390347972619272†L31-L34】【692691223795286†L68-L98】. Die Aufgaben in diesem Bereich führen dich von einfachen Ratespielen über Rogue‑Like‑Titel bis hin zu 3D‑Raycasting und künstlicher Intelligenz.'
  ),
  (
    'Web Design',
    'Fokus auf Benutzererfahrung und Ästhetik; schnelle Erfolgserlebnisse; grosse Nachfrage nach guten Designs.',
    'Erfordert Gespür für Design; ständige Trends; kann weniger technisch herausfordernd sein.',
    'Webdesign ist ein vielschichtiges Feld, das kreative, technische und organisatorische Fähigkeiten vereint. Webdesigner gestalten das visuelle Layout und die Interaktion einer Website, indem sie Elemente wie Layout, Farben, Typografie, Bilder und Navigation kombinieren, um eine ansprechende und zugängliche Benutzererfahrung zu schaffen【294775985102773†L27-L50】. Zu ihren Aufgaben gehört es, Seitenlayouts zu entwerfen, die Navigation zu planen, Mockups zu erstellen, Domain und Hosting zu verwalten sowie Design‑Assets zu organisieren und mit Redakteuren und Entwicklern zusammenzuarbeiten【294775985102773†L52-L77】. Neben Kreativität sind kommunikative Fähigkeiten, Zeitmanagement und ein Verständnis von UX‑Prinzipien sowie grundlegende Kenntnisse in HTML/CSS erforderlich【294775985102773†L116-L154】. Unsere Aufgaben starten mit Tribute‑Pages und Formularen und führen über Parallax‑Scrolling bis hin zu responsiven Navigationsleisten und CSS‑Animationen.'
  ),
  (
    'Web Development',
    'Hohe Nachfrage, vielfältige Karrierewege (Frontend, Backend, Full‑Stack); stetiger Fortschritt in Technologien.',
    'Schnelllebiger Technologiewandel; braucht breite Kenntnisse vom Server bis zum Client.',
    'Webentwicklung konzentriert sich auf die Erstellung und Wartung von Websites und Web‑Anwendungen. Webentwickler schreiben und pflegen Code für das Backend mit Fokus auf Architektur, Performance und Skalierbarkeit【950670679926012†L126-L131】. Backend‑Entwickler erstellen die Struktur und sorgen für zuverlässige Abläufe【950670679926012†L169-L174】, Frontend‑Entwickler gestalten die Benutzeroberfläche mit HTML, CSS, JavaScript und modernen Frameworks wie React, Vue oder Angular【950670679926012†L178-L183】, und Full‑Stack‑Entwickler verbinden beide Rollen【950670679926012†L186-L190】. Dieser Bereich bietet zahlreiche Karriereoptionen und erfordert eine Mischung aus technischen Fähigkeiten, Problemlösungskompetenz und Teamarbeit. Die Projekte reichen von kleinen Event‑Seiten über Tools mit lokalem Speicher bis hin zu Full‑Stack‑Anwendungen und API‑Integration.'
  );

-- Aufgaben fuer C Programmierung (ehemalige Level 1)
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='C Programmierung'), 1, 'Fibonacci‑Generator',
    'Schreibe ein Programm, das die ersten N Fibonacci‑Zahlen berechnet und ausgibt. Beispiel: Bei N=7 → 0, 1, 1, 2, 3, 5, 8',
    'Beginne mit zwei Variablen a=0 und b=1. Wiederhole: Gib a aus, setze a=b und b=a+b.',
    'tasks/001_fibonacci_generator.c'),
  ((SELECT id FROM areas WHERE name='C Programmierung'), 1, 'Primzahl‑Checker',
    'Erstelle ein Programm, das prüft, ob eine eingegebene Zahl eine Primzahl ist.',
    'Eine Primzahl ist nur durch 1 und sich selbst teilbar. Prüfe die Teilbarkeit nur bis zur Quadratwurzel der Zahl.',
    'tasks/002_primzahl_checker.c'),
  ((SELECT id FROM areas WHERE name='C Programmierung'), 1, 'Palindrom‑Prüfer',
    'Prüfe, ob ein eingegebenes Wort ein Palindrom ist (vorwärts = rückwärts gleich).',
    'Vergleiche die Zeichen des Strings von aussen nach innen.',
    'tasks/003_palindrom_pruefer.c'),
  ((SELECT id FROM areas WHERE name='C Programmierung'), 1, 'Zahlen‑Raten‑Spiel',
    'Programmiere ein Ratespiel: Der Computer wählt eine Zufallszahl zwischen 1 und 100, der Spieler muss raten.',
    'Nutze rand(), srand(time(NULL)) und gebe Hinweise wie „Zu hoch!“ oder „Zu niedrig!“.',
    'tasks/004_zahlen_raten_spiel.c'),
  ((SELECT id FROM areas WHERE name='C Programmierung'), 2, 'Caesar‑Verschlüsselung',
    'Implementiere die Caesar‑Verschlüsselung (Buchstaben um N Positionen verschieben).',
    'Verwende ASCII‑Werte der Buchstaben und den Modulo‑Operator für den Umbruch.',
    'tasks/005_caesar_verschluesselung.c');

-- Aufgaben fuer Game Development (beispielhaft)
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Game Development'), 1, 'Guessing Game',
    'Programmiere ein Konsolenspiel, bei dem der Computer eine Zufallszahl auswählt und der Spieler sie errät. Nach jedem Rateversuch wird „höher“ oder „niedriger“ ausgegeben.',
    'Nutze rand() und gib nach jedem Rateversuch an, ob die Zahl höher oder niedriger ist.',
    'tasks/game_guessing_game.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 2, 'Text Adventure (Zork‑like)',
    'Entwickle ein einfaches Text‑Abenteuer mit verzweigten Entscheidungen. Der Spieler navigiert durch eine Welt durch Texteingaben und Entscheidungen.',
    'Strukturiere die Welt und Entscheidungen in Datenstrukturen (z.B. Structs) und verwende Schleifen/Switch-Anweisungen, um die Entscheidungen zu verarbeiten.',
    'tasks/game_text_adventure.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 3, 'Rogue‑Like',
    'Erstelle einen einfachen „Rogue‑Like“ Dungeon‑Crawler. Generiere zufällige Räume und Flure, lasse den Spieler Monster bekämpfen und Gegenstände aufsammeln.',
    'Beginne mit der zufälligen Generierung eines Spielfelds aus Räumen und Korridoren. Implementiere dann Bewegung, einfache Kämpfe und Inventar.',
    'tasks/game_roguelike.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 2, 'Pong‑Spiel',
    'Programmiere das klassische Pong‑Spiel mit zwei Schlägern und einem Ball. Zähle Punkte und ermögliche ein Reset.',
    'Nutze ein Framework oder eine Bibliothek wie SDL oder SFML (oder eine Konsolen‑Variante) und implementiere Ballbewegung, Kollisionsabfrage und Punktestand.',
    'tasks/game_pong.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 2, 'Tic‑Tac‑Toe',
    'Implementiere Tic‑Tac‑Toe mit einem Computergegner. Optimiere den Gegner so, dass er nicht besiegt werden kann.',
    'Stelle das Spielfeld als Array dar und implementiere Minimax oder einfache Regeln für den Computergegner.',
    'tasks/game_tictactoe.c');

-- Aufgaben fuer Web Design (beispielhaft aus GeeksforGeeks)
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Web Design'), 1, 'Tribute Page',
    'Erstelle eine einfache Tribute‑Page für eine Person. Füge ein Bild, den Namen der Person und einen Abschnitt mit Informationen hinzu. Verwende HTML‑Elemente wie Überschriften, Absätze, Listen und Links sowie einfache CSS‑Stile.',
    'Nutze grundlegende HTML‑Tags wie <header>, <main>, <section> und CSS zur Gestaltung. Achte auf ein ansprechendes Layout.',
    'tasks/webdesign_tribute_page.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 1, 'Webseite mit Formular',
    'Entwickle eine Webseite mit einem Formular (z. B. Umfrage oder Kontaktformular). Verwende verschiedene Formularelemente wie Textfelder, Checkboxen, Radiobuttons und Dropdowns.',
    'Verwende das <form>-Element und HTML5‑Formularattribute. Ergänze CSS für Layout und Styling.',
    'tasks/webdesign_form_page.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 2, 'Parallax‑Website',
    'Erstelle eine Parallax‑Scrolling‑Webseite mit mehreren Abschnitten und Hintergrundbildern, die sich unterschiedlich schnell bewegen.',
    'Nutze CSS Eigenschaften wie background-attachment und position, oder JavaScript, um den Parallax‑Effekt zu erzielen.',
    'tasks/webdesign_parallax_page.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 2, 'Landing Page',
    'Gestalte eine moderne Landing Page mit Header, Footer, mehreren Sektionen und Call‑to‑Action Buttons. Achte auf ein responsives Layout.',
    'Strukturiere die Seite in Abschnitte und verwende Flexbox oder CSS Grid für das Layout. Wähle passende Farben und Schriftarten.',
    'tasks/webdesign_landing_page.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 3, 'Restaurant‑Website',
    'Entwirf eine Restaurant‑Website mit Menükarten, Bildern, einem Reservierungsformular und Kontaktinformationen. Die Seite sollte responsiv sein.',
    'Verwende CSS Grid oder Flexbox für das Layout. Integriere ein Kontaktformular und gestalte die Seite ansprechend.',
    'tasks/webdesign_restaurant_page.html');

-- Aufgaben fuer Web Development (beispielhaft – Frontend & Backend)
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Web Development'), 1, 'Event Webseite',
    'Baue eine einfache Event‑Webseite mit HTML und CSS. Zeige Informationen wie Datum, Ort und Beschreibung an.',
    'Verwende semantische HTML‑Elemente wie <section> und <article>. Gestalte das Layout mit CSS.',
    'tasks/webdev_event_page.html'),
  ((SELECT id FROM areas WHERE name='Web Development'), 1, 'Portfolio‑Galerie',
    'Erstelle eine Portfolio‑Galerie, die Bilder und kurze Beschreibungen enthält. Nutze CSS Grid oder Flexbox für das Layout.',
    'Lade mehrere Bilder und ordne sie in einem responsiven Raster an. Verwende Übergänge und Hover‑Effekte.',
    'tasks/webdev_portfolio_gallery.html'),
  ((SELECT id FROM areas WHERE name='Web Development'), 1, 'Lorem Ipsum Generator',
    'Programmiere ein kleines Tool, das eine frei wählbare Anzahl von Lorem‑Ipsum‑Absätzen generiert (Frontend‑JavaScript).',
    'Nutze JavaScript, um einen Button und ein Eingabefeld abzufangen. Erzeuge dann dynamisch Textabsätze.',
    'tasks/webdev_lorem_ipsum.js'),
  ((SELECT id FROM areas WHERE name='Web Development'), 2, 'Preis‑Bereich‑Slider',
    'Implementiere einen interaktiven Preis‑Range‑Slider mit HTML, CSS und JavaScript.',
    'Verwende das <input type="range">‑Element und JavaScript, um den aktuellen Wert anzuzeigen.',
    'tasks/webdev_price_slider.html'),
  ((SELECT id FROM areas WHERE name='Web Development'), 2, 'Multi‑Step Progress Bar',
    'Erstelle eine Webseite mit einem Multi‑Step Progress Bar, das den Fortschritt in mehreren Schritten anzeigt.',
    'Nutze CSS und JavaScript, um die Schritte zu markieren und den Fortschritt visuell darzustellen.',
    'tasks/webdev_progress_bar.html');

-- Zusätzliche Aufgaben für Game Development
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Game Development'), 3, 'Breakout‑Spiel',
    'Implementiere ein Breakout-Spiel mit Ball, Schläger und Ziegelsteinen. Der Ball prallt vom Schläger ab und zerstört Ziegel.',
    'Nutze eine Bibliothek wie SDL oder SFML oder eine Konsolendarstellung. Verwende Arrays für die Ziegel und berechne Kollisionen.',
    'tasks/game_breakout.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 4, 'Plattformspiel',
    'Erstelle ein einfaches 2D-Plattformspiel mit Sprungmechanik, Hindernissen und Sammelobjekten.',
    'Implementiere Schwerkraft und Kollisionserkennung. Verwende eine Tile‑Map für die Welt.',
    'tasks/game_platformer.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 5, '3D Raycaster',
    'Schreibe eine einfache Raycasting-Engine, um eine 3D-Darstellung eines 2D-Labyrinths wie in Wolfenstein 3D zu erzeugen.',
    'Beginne mit einer 2D-Karte und wirf Strahlen in Blickrichtung. Berechne Wandabstände und skaliere die Darstellung.',
    'tasks/game_3d_raycaster.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 4, 'Schach mit KI',
    'Programmiere ein textbasiertes Schachspiel und implementiere eine einfache Minimax-KI.',
    'Beginne mit der Darstellung des Schachbretts und der Zugregeln. Für die KI kann Minimax mit begrenzter Tiefe genutzt werden.',
    'tasks/game_chess_ai.c'),
  ((SELECT id FROM areas WHERE name='Game Development'), 4, 'Tower Defense',
    'Erstelle ein Tower‑Defense‑Spiel mit verschiedenen Türmen und Gegnern. Die Gegner folgen einem vorgegebenen Pfad.',
    'Definiere Datenstrukturen für Türme und Gegner. Implementiere eine Spielschleife und Kollisionserkennung.',
    'tasks/game_tower_defense.c');

-- Zusätzliche Aufgaben für Web Design
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Web Design'), 1, 'Responsive Navigation Bar',
    'Baue eine responsive Navigationsleiste, die auf kleinen Bildschirmen zu einem Hamburger‑Menü zusammenfällt.',
    'Nutze CSS Media Queries und JavaScript, um das Menü anzuzeigen oder zu verstecken.',
    'tasks/webdesign_responsive_navbar.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 2, 'Card Layout',
    'Erstelle ein ansprechendes Kartenlayout für eine Galerie oder Produktübersicht.',
    'Verwende CSS Flexbox oder Grid, um Karten responsiv anzuordnen.',
    'tasks/webdesign_card_layout.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 2, 'Bild‑Karussell',
    'Baue ein Bildkarussell, das zwischen mehreren Bildern wechselt.',
    'Nutze CSS zur Gestaltung und JavaScript zur Steuerung der Slides.',
    'tasks/webdesign_image_carousel.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 1, 'Modal Dialog',
    'Implementiere einen Modal-Dialog, der über der Seite schwebt.',
    'Nutze CSS für das Layout und JavaScript, um das Modal zu öffnen und zu schließen.',
    'tasks/webdesign_modal.html'),
  ((SELECT id FROM areas WHERE name='Web Design'), 3, 'CSS Animationen',
    'Erstelle eine Demo mit CSS-Animationen (z. B. Springen, Pulsieren, Drehen).',
    'Nutze @keyframes und CSS-Animationen, um Elemente in Bewegung zu versetzen.',
    'tasks/webdesign_css_animation.html');

-- Zusätzliche Aufgaben für Web Development
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Web Development'), 2, 'GitHub‑Profil‑Suche',
    'Erstelle eine kleine Anwendung, die mithilfe der GitHub‑API Informationen zu einem Benutzer abruft und anzeigt.',
    'Nutze fetch() zum Abrufen der API und verarbeite die JSON‑Antwort für den Benutzer.',
    'tasks/webdev_github_profile_search.js'),
  ((SELECT id FROM areas WHERE name='Web Development'), 1, 'Notizen‑App mit localStorage',
    'Programmiere eine Notizen-App, die Notizen im Browser mit localStorage speichert.',
    'Verwende JavaScript, um Einträge hinzuzufügen, zu löschen und anzuzeigen. Speichere die Notizen als JSON.',
    'tasks/webdev_notes_app.js'),
  ((SELECT id FROM areas WHERE name='Web Development'), 3, 'Chat‑Anwendung (Frontend)',
    'Baue ein einfaches Chat‑Frontend mit HTML und JavaScript. Verwende WebSockets für die Kommunikation (Server muss separat erstellt werden).',
    'Erstelle eine Benutzeroberfläche für Chatnachrichten und verwende die WebSocket API, um Nachrichten zu senden und zu empfangen.',
    'tasks/webdev_chat_app.html'),
  ((SELECT id FROM areas WHERE name='Web Development'), 3, 'Full‑Stack ToDo‑App',
    'Konzipiere eine Full‑Stack‑ToDo‑Anwendung mit Frontend, Backend und Datenbank.',
    'Trenne Client und Server; nutze eine Datenbank zur persistenten Speicherung der Aufgaben. Definiere REST‑Endpunkte.',
    'tasks/webdev_todo_fullstack.md'),
  ((SELECT id FROM areas WHERE name='Web Development'), 2, 'Wetter‑App',
    'Entwickle eine Wetter‑App, die aktuelle Wetterdaten für eine ausgewählte Stadt anzeigt.',
    'Nutze fetch() und die OpenWeatherMap API (oder eine andere) und zeige Temperatur, Beschreibung und Symbole an.',
    'tasks/webdev_weather_app.js');

-- Neue Bereiche fuer Game Designer und Programmieren Lernen

INSERT INTO areas (name, strengths, weaknesses, description) VALUES
  ('Game Designer',
    'Fördert kreative Erzählungen und Mechaniken; vermittelt Fähigkeiten zur Konzeption spannender Spielerlebnisse; design‑orientiertes Denken.',
    'Begrenzte Stellenangebote; hohe Konkurrenz; verlangt starke Zusammenarbeit mit technischen Teams und viel Iteration.',
    'Game Design konzentriert sich auf die Entwicklung der Spielidee, der Geschichte, der Charaktere und der grundlegenden Mechaniken. In diesem Bereich lernst du, wie man ein Spielkonzept skizziert, Level strukturiert, Charaktere entwirft, Mechaniken definiert und daraus ein vollständiges Design‑Dokument erstellt. Diese Fähigkeiten bilden die Grundlage für eine gute Spielbalance und fesselnde Spielerlebnisse.'
  ),
  ('Programmieren Lernen',
    'Vermittelt eine solide Basis; für Anfänger geeignet; breit anwendbar; fördert problemlösendes Denken.',
    'Einsteigeraufgaben können repetitiv wirken; fehlt anfangs der Praxisbezug; ohne Herausforderung wird es schnell langweilig.',
    'Dieser Bereich richtet sich an absolute Anfänger und vermittelt grundlegende Konzepte der Programmierung. Hier lernst du, einfache Programme zu schreiben, Variablen zu verwenden, Eingaben auszulesen, Schleifen und Bedingungen zu implementieren sowie Funktionen zu definieren. Diese Grundlagen sind erforderlich, um später komplexere Anwendungen zu entwickeln und in andere Bereiche wie Web‑ oder Spieleentwicklung einzusteigen.'
  );

-- Aufgaben fuer den Bereich Game Designer
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Game Designer'), 1, 'Spielkonzept skizzieren',
    'Schreibe eine kurze Spielidee inklusive Thema, Genre und Hauptmechanik. Beschreibe das Ziel des Spiels und warum es Spass macht.',
    'Beginne mit einem Satz, der das Thema und die Grundidee beschreibt. Füge dann Genre und Ziel hinzu.',
    'tasks/gamedesign_concept.txt'),
  ((SELECT id FROM areas WHERE name='Game Designer'), 1, 'Level‑Design dokumentieren',
    'Beschreibe den Aufbau eines einfachen Levels. Gib Startpunkt, Hindernisse, Gegner und Ziel klar an.',
    'Skizziere den Weg von Anfang bis Ende. Nenne wichtige Interaktionen und Herausforderungen.',
    'tasks/gamedesign_level.txt'),
  ((SELECT id FROM areas WHERE name='Game Designer'), 1, 'Charakter‑Konzept',
    'Entwirf einen Hauptcharakter: Name, Hintergrundgeschichte, Fähigkeiten und Motivation.',
    'Überlege dir, wer der Charakter ist, was ihn antreibt und welche besonderen Eigenschaften er hat.',
    'tasks/gamedesign_character.txt'),
  ((SELECT id FROM areas WHERE name='Game Designer'), 2, 'Gameplay‑Mechanik formulieren',
    'Beschreibe eine Kernmechanik deines Spiels (z. B. Bewegung, Kampf, Puzzles). Erkläre, wie sie funktioniert und warum sie Spass macht.',
    'Definiere die Eingaben des Spielers und die Reaktion des Spiels. Erkläre, wie diese Mechanik mit anderen Mechaniken zusammenwirkt.',
    'tasks/gamedesign_mechanic.txt'),
  ((SELECT id FROM areas WHERE name='Game Designer'), 2, 'Game Design Dokument',
    'Erstelle ein kurzes Game Design Dokument mit den Abschnitten: Konzept, Spielmechaniken, Charaktere und Levelprogression.',
    'Nutze eine klare Gliederung: Beginne mit dem Konzept, beschreibe dann die Mechaniken, stelle die Hauptcharaktere vor und skizziere den Verlauf durch die Level.',
    'tasks/gamedesign_document.txt');

-- Aufgaben fuer den Bereich Programmieren Lernen
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Programmieren Lernen'), 1, 'Hallo‑Welt‑Programm',
    'Schreibe ein Programm, das “Hallo, Welt!” auf der Konsole ausgibt.',
    'Nutze die Funktion printf(), um den Text auszugeben.',
    'tasks/programming_hello_world.c'),
  ((SELECT id FROM areas WHERE name='Programmieren Lernen'), 1, 'Einfache Addition',
    'Lies zwei ganze Zahlen ein und gib ihre Summe aus.',
    'Verwende scanf(), um Eingaben zu lesen, und berechne die Summe mit dem +‑Operator.',
    'tasks/programming_addition.c'),
  ((SELECT id FROM areas WHERE name='Programmieren Lernen'), 1, 'Gerade oder ungerade?',
    'Überprüfe, ob eine eingegebene Zahl gerade oder ungerade ist.',
    'Nutze den Modulo‑Operator (%) und vergleiche mit Null.',
    'tasks/programming_even_odd.c'),
  ((SELECT id FROM areas WHERE name='Programmieren Lernen'), 1, 'For‑Schleife 1 bis 10',
    'Gib die Zahlen 1 bis 10 mit einer for‑Schleife aus.',
    'Initialisiere einen Zähler bei 1, erhöhe ihn bis 10 und gib ihn aus.',
    'tasks/programming_loop_1_to_10.c'),
  ((SELECT id FROM areas WHERE name='Programmieren Lernen'), 1, 'Quadrat‑Funktion',
    'Schreibe eine Funktion, die eine Zahl als Parameter erhält und das Quadrat dieser Zahl zurückgibt.',
    'Definiere eine Funktion, die den Parameter mit sich selbst multipliziert und das Ergebnis mit return zurückgibt.',
    'tasks/programming_square_function.c');

-- Neue Bereiche fuer weitere Programmiersprachen

INSERT INTO areas (name, strengths, weaknesses, description) VALUES
  ('Java Programmierung',
    'Weit verbreitet, objektorientiert, grosse Bibliotheksvielfalt, plattformunabhängig dank der JVM.',
    'Kann im Vergleich zu anderen Sprachen mehr Boilerplate erfordern; Performance overhead der JVM.',
    'Java ist eine objektorientierte Programmiersprache, die besonders für grosse Unternehmensanwendungen beliebt ist. In diesem Bereich lernst du grundlegende Sprachkonstrukte, Kontrollstrukturen, Klassen und Methoden. Spätere Aufgaben behandeln Datenstrukturen, Fehlerbehandlung und die Nutzung gängiger Java‑Bibliotheken.'),
  ('Python Programmierung',
    'Einfache, ausdrucksstarke Syntax; grosse Community; vielseitig einsetzbar (Web, Data Science, Automatisierung).',
    'Langsamer als kompilierte Sprachen; dynamische Typisierung kann zu Laufzeitfehlern führen.',
    'Python zeichnet sich durch eine leicht verständliche Syntax aus. Hier lernst du grundlegende Konzepte wie Variablen, Kontrollstrukturen, Funktionen und Module. Später folgen Aufgaben aus Web‑Entwicklung, Data Science und Automatisierung.'),
  ('PHP Programmierung',
    'Serverseitige Skriptsprache; einfache Integration in HTML; weit verbreitet im Web.',
    'Historisch uneinheitliche Syntax; inkonsistente Standardfunktionen; weniger beliebt für neue Projekte.',
    'PHP ist eine serverseitige Sprache, die häufig zur Erstellung dynamischer Webseiten verwendet wird. Dieser Bereich führt dich von einfachen Skripten über Formulareingaben bis hin zu Datenbankanbindungen.'),
  ('HTML & CSS',
    'Fundamental für Webentwicklung; lässt sich leicht erlernen; plattformunabhängig.',
    'Begrenzte Logik; zur Interaktion mit dem Server sind zusätzliche Sprachen erforderlich.',
    'HTML und CSS bilden die Grundlage jeder Webseite. In diesem Bereich lernst du, wie man Dokumente strukturiert, Inhalte semantisch markiert, Layouts erstellt und mit CSS gestaltet. Spätere Aufgaben behandeln responsive Design und Animationen.');

-- Aufgaben fuer Java Programmierung
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Java Programmierung'), 1, 'Hello World in Java',
    'Schreibe ein Java‑Programm, das "Hallo, Welt!" ausgibt.',
    'Definiere eine Klasse mit der Methode main(String[] args) und verwende System.out.println().',
    'tasks/java_hello_world.java'),
  ((SELECT id FROM areas WHERE name='Java Programmierung'), 1, 'Addition zweier Zahlen (Java)',
    'Lies zwei ganze Zahlen von der Konsole und gib ihre Summe aus.',
    'Nutze java.util.Scanner zum Einlesen und addiere die Werte.',
    'tasks/java_addition.java'),
  ((SELECT id FROM areas WHERE name='Java Programmierung'), 1, 'Gerade oder ungerade? (Java)',
    'Schreibe ein Programm, das prüft, ob eine Zahl gerade oder ungerade ist.',
    'Verwende den Modulo‑Operator % und eine if‑Anweisung.',
    'tasks/java_even_odd.java'),
  ((SELECT id FROM areas WHERE name='Java Programmierung'), 1, 'Schleife 1–10 (Java)',
    'Gib mit einer for‑Schleife die Zahlen 1 bis 10 aus.',
    'Initialisiere einen Zähler bei 1, prüfe bis 10 und erhöhe ihn.',
    'tasks/java_loop_1_to_10.java'),
  ((SELECT id FROM areas WHERE name='Java Programmierung'), 1, 'Quadrat‑Funktion (Java)',
    'Definiere eine Methode, die eine Ganzzahl entgegen nimmt und ihr Quadrat zurückgibt.',
    'Die Methode multipliziert den Parameter mit sich selbst und liefert das Ergebnis zurück.',
    'tasks/java_square_function.java');

-- Aufgaben fuer Python Programmierung
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='Python Programmierung'), 1, 'Hello World in Python',
    'Schreibe ein Python‑Programm, das "Hallo, Welt!" ausgibt.',
    'Nutze print("Hallo, Welt!") für die Ausgabe.',
    'tasks/python_hello_world.py'),
  ((SELECT id FROM areas WHERE name='Python Programmierung'), 1, 'Addition zweier Zahlen (Python)',
    'Lies zwei Zahlen mit input() ein, wandle sie in int um und gib ihre Summe aus.',
    'Verwende int(input()) und addiere die Variablen.',
    'tasks/python_addition.py'),
  ((SELECT id FROM areas WHERE name='Python Programmierung'), 1, 'Gerade oder ungerade? (Python)',
    'Prüfe, ob eine eingegebene Zahl gerade oder ungerade ist.',
    'Nutze den Modulo‑Operator % und eine if‑Else‑Struktur.',
    'tasks/python_even_odd.py'),
  ((SELECT id FROM areas WHERE name='Python Programmierung'), 1, 'Schleife 1–10 (Python)',
    'Gib die Zahlen 1 bis 10 mit einer for‑Schleife aus.',
    'Verwende for i in range(1, 11): print(i).',
    'tasks/python_loop_1_to_10.py'),
  ((SELECT id FROM areas WHERE name='Python Programmierung'), 1, 'Quadrat‑Funktion (Python)',
    'Schreibe eine Funktion square(n), die n * n zurückgibt, und demonstriere sie.',
    'Definiere def square(n): return n*n und rufe sie auf.',
    'tasks/python_square_function.py');

-- Aufgaben fuer PHP Programmierung
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='PHP Programmierung'), 1, 'Hello World in PHP',
    'Schreibe ein PHP‑Skript, das "Hallo, Welt!" ausgibt.',
    'Nutze echo "Hallo, Welt!";',
    'tasks/php_hello_world.php'),
  ((SELECT id FROM areas WHERE name='PHP Programmierung'), 1, 'Addition zweier Zahlen (PHP)',
    'Lies zwei Ganzzahlen per Eingabe (CLI) und gib ihre Summe aus.',
    'Nutze readline() zum Lesen und casten mit (int).',
    'tasks/php_addition.php'),
  ((SELECT id FROM areas WHERE name='PHP Programmierung'), 1, 'Gerade oder ungerade? (PHP)',
    'Erstelle ein Skript, das prüft, ob eine Zahl gerade oder ungerade ist.',
    'Nutze den Modulo‑Operator % und eine if‑Bedingung.',
    'tasks/php_even_odd.php'),
  ((SELECT id FROM areas WHERE name='PHP Programmierung'), 1, 'Schleife 1–10 (PHP)',
    'Gib die Zahlen 1 bis 10 mithilfe einer for‑Schleife aus.',
    'Verwende for($i=1; $i<=10; $i++) echo $i;',
    'tasks/php_loop_1_to_10.php'),
  ((SELECT id FROM areas WHERE name='PHP Programmierung'), 1, 'Quadrat‑Funktion (PHP)',
    'Schreibe eine Funktion square($n), die das Quadrat zurückgibt, und verwende sie.',
    'Definiere function square($n) { return $n*$n; } und rufe sie auf.',
    'tasks/php_square_function.php');

-- Aufgaben fuer HTML & CSS
INSERT INTO tasks (area_id, difficulty, title, description, hint, solution_file) VALUES
  ((SELECT id FROM areas WHERE name='HTML & CSS'), 1, 'Einfache HTML‑Seite',
    'Erstelle eine HTML‑Seite mit einem Titel, einer Überschrift und einem Absatz.',
    'Nutze die Tags <!DOCTYPE html>, <html>, <head>, <title>, <body>, <h1> und <p>.',
    'tasks/html_simple_page.html'),
  ((SELECT id FROM areas WHERE name='HTML & CSS'), 1, 'Liste und Link',
    'Erstelle eine HTML‑Seite mit einer ungeordneten Liste (ul) und drei Listenelementen sowie einem Link.',
    'Verwende <ul>, <li> und <a href="...">. Achte auf die semantische Struktur.',
    'tasks/html_list_link.html'),
  ((SELECT id FROM areas WHERE name='HTML & CSS'), 1, 'Formular mit CSS',
    'Erstelle ein Kontaktformular mit Eingabefeldern für Name, E‑Mail und Nachricht. Gestalte es mit CSS.',
    'Nutze das <form>‑Element und CSS‑Eigenschaften wie margin, padding und background‑color.',
    'tasks/html_form.html'),
  ((SELECT id FROM areas WHERE name='HTML & CSS'), 1, 'Tabelle erstellen',
    'Erstelle eine Tabelle mit drei Zeilen und zwei Spalten und gestalte die Tabelle mit CSS.',
    'Verwende <table>, <tr>, <td> und CSS für Rahmen und Hintergrund.',
    'tasks/html_table.html'),
  ((SELECT id FROM areas WHERE name='HTML & CSS'), 1, 'Responsive Layout',
    'Erstelle ein einfaches zweispaltiges Layout, das sich auf mobilen Geräten untereinander anordnet.',
    'Nutze CSS Flexbox oder Grid und Media Queries für die Responsivität.',
    'tasks/html_responsive_layout.html');