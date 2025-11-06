#!/usr/bin/env python3
"""
This script generates a C programming portfolio consisting of separate
source files for each exercise and a static HTML page to browse them.

Each exercise is stored in its own C source file under the ``tasks``
subdirectory. The HTML page lists all exercises with their title,
description and a link to the corresponding source file. A simple
search/filter functionality is provided via JavaScript.

Only a subset of exercises (1–35) contain fully implemented solutions.
The remaining tasks contain skeletons with TODO markers so the user
can extend them over time.

To regenerate the portfolio, simply run this script from the
``portfolio`` directory. It will overwrite the existing ``tasks``
folder, ``index.html``, ``style.css`` and ``script.js``.
"""
import os
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent
TASKS_DIR = ROOT / "tasks"

# Create tasks directory if it does not exist
if not TASKS_DIR.exists():
    TASKS_DIR.mkdir(parents=True)
# Remove existing files in tasks directory
for item in TASKS_DIR.iterdir():
    if item.is_file():
        item.unlink()

def slugify(s: str) -> str:
    s = s.lower()
    s = re.sub(r"[äÄ]", "ae", s)
    s = re.sub(r"[öÖ]", "oe", s)
    s = re.sub(r"[üÜ]", "ue", s)
    s = re.sub(r"ß", "ss", s)
    s = re.sub(r"[^a-z0-9]+", "_", s)
    s = s.strip("_")
    return s

# tasks_data will hold dictionaries with id, title, description, code
tasks_data = []

def add_task(task_id: int, title: str, description: str, code: str = None):
    tasks_data.append({
        "id": task_id,
        "title": title,
        "description": description.strip(),
        "code": code
    })

# Add tasks 1–35 with implementations
add_task(1, "Fibonacci-Generator",
         "Schreibe ein Programm, das die ersten N Fibonacci-Zahlen berechnet und ausgibt. Beispiel: Bei N=7 → 0, 1, 1, 2, 3, 5, 8.",
         """
/*
 * Task 1: Fibonacci-Generator
 * Beschreibung: Dieses Programm berechnet die ersten N Fibonacci-Zahlen
 * und gibt sie durch Komma getrennt aus. N wird vom Benutzer eingegeben.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("N? ");
    if (scanf("%d", &n) != 1 || n < 0) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    long long a = 0, b = 1;
    for (int i = 0; i < n; i++) {
        printf("%lld%s", a, (i == n - 1) ? "" : ", ");
        long long next = a + b;
        a = b;
        b = next;
    }
    printf("\n");
    return 0;
}
"""
)

add_task(2, "Primzahl-Checker",
         "Erstelle ein Programm, das prüft, ob eine eingegebene Zahl eine Primzahl ist. Eine Primzahl ist nur durch 1 und sich selbst teilbar.",
         """
/*
 * Task 2: Primzahl-Checker
 * Beschreibung: Dieses Programm liest eine Zahl ein und überprüft,
 * ob sie eine Primzahl ist. Zur Optimierung wird nur bis zur Wurzel
 * der Zahl getestet.
 */
#include <stdio.h>
#include <math.h>

int main(void) {
    long long n;
    printf("Zahl? ");
    if (scanf("%lld", &n) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    if (n < 2) {
        printf("%lld ist keine Primzahl.\n", n);
        return 0;
    }
    if (n % 2 == 0) {
        printf("%lld ist %sPrimzahl.\n", n, (n == 2) ? "eine " : "keine ");
        return 0;
    }
    int prim = 1;
    for (long long i = 3; i * i <= n; i += 2) {
        if (n % i == 0) {
            prim = 0;
            break;
        }
    }
    printf("%lld ist %sPrimzahl.\n", n, prim ? "eine " : "keine ");
    return 0;
}
"""
)

add_task(3, "Palindrom-Prüfer",
         "Prüfe, ob ein eingegebenes Wort ein Palindrom ist (vorwärts = rückwärts gleich).",
         """
/*
 * Task 3: Palindrom-Pruefer
 * Beschreibung: Dieses Programm prüft, ob ein eingegebenes Wort
 * ein Palindrom ist, d.h. vorwärts wie rückwärts gleich gelesen wird.
 */
#include <stdio.h>
#include <string.h>

int main(void) {
    char s[256];
    printf("Wort? ");
    if (!fgets(s, sizeof(s), stdin)) {
        return 0;
    }
    // Newline entfernen
    size_t len = strlen(s);
    if (len > 0 && s[len - 1] == '\n') s[len - 1] = '\0';
    int left = 0;
    int right = (int)strlen(s) - 1;
    int pal = 1;
    while (left < right) {
        if (s[left] != s[right]) {
            pal = 0;
            break;
        }
        left++;
        right--;
    }
    printf("\"%s\" ist %s Palindrom.\n", s, pal ? "ein" : "kein");
    return 0;
}
"""
)

add_task(4, "Zahlen-Raten-Spiel",
         "Programmiere ein Ratespiel: Der Computer wählt eine Zufallszahl (1-100) und der Spieler muss raten. Der Spieler erhält Hinweise 'Zu hoch!' oder 'Zu niedrig!' und die Anzahl der Versuche wird gezählt.",
         """
/*
 * Task 4: Zahlen-Raten-Spiel
 * Beschreibung: Der Computer denkt sich eine Zufallszahl zwischen 1 und 100 aus,
 * und der Benutzer versucht, die Zahl zu erraten. Nach jedem Tipp erhält der
 * Benutzer einen Hinweis, ob die Zahl zu hoch oder zu niedrig ist. Die Anzahl
 * der Versuche wird gezählt.
 */
#include <stdio.h>
#include <stdlib.h>
#include <time.h>

int main(void) {
    srand((unsigned)time(NULL));
    int target = 1 + rand() % 100;
    int guess = 0;
    int tries = 0;
    printf("Ich denke mir eine Zahl zwischen 1 und 100. Versuche sie zu erraten.\n");
    while (1) {
        printf("Tipp? ");
        if (scanf("%d", &guess) != 1) {
            printf("Ungueltige Eingabe.\n");
            // Eingabepuffer leeren
            int c;
            while ((c = getchar()) != '\n' && c != EOF) {}
            continue;
        }
        tries++;
        if (guess < target) {
            printf("Zu niedrig!\n");
        } else if (guess > target) {
            printf("Zu hoch!\n");
        } else {
            printf("Richtig! Du hast die Zahl in %d Versuchen erraten.\n", tries);
            break;
        }
    }
    return 0;
}
"""
)

add_task(5, "Caesar-Verschlüsselung",
         "Implementiere die Caesar-Verschlüsselung: Buchstaben werden um N Positionen verschoben. Beispiel: 'HALLO' mit Verschiebung 3 → 'KDOOR'.",
         """
/*
 * Task 5: Caesar-Verschluesselung
 * Beschreibung: Dieses Programm verschlüsselt einen eingegebenen Text mit
 * einem Caesar-Shift. Gross- und Kleinbuchstaben werden getrennt behandelt.
 */
#include <stdio.h>
#include <ctype.h>

// Hilfsfunktion, um einen einzelnen Buchstaben zu verschieben
static char shift_char(char c, int k) {
    if ('A' <= c && c <= 'Z') {
        return (char)('A' + ((c - 'A' + k) % 26 + 26) % 26);
    }
    if ('a' <= c && c <= 'z') {
        return (char)('a' + ((c - 'a' + k) % 26 + 26) % 26);
    }
    return c;
}

int main(void) {
    int k;
    char text[512];
    printf("Verschiebung k? ");
    if (scanf("%d", &k) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    // Eingabepuffer leeren, um newline zu entfernen
    int c;
    while ((c = getchar()) != '\n' && c != EOF) {}
    printf("Text: ");
    if (!fgets(text, sizeof(text), stdin)) {
        return 0;
    }
    for (size_t i = 0; text[i] != '\0'; i++) {
        text[i] = shift_char(text[i], k);
    }
    printf("Verschluesselter Text: %s\n", text);
    return 0;
}
"""
)

# Tasks 6–10
add_task(6, "Wörter zählen",
         "Zähle die Anzahl der Wörter in einem eingegebenen Satz. Mehrfache Leerzeichen sollen ignoriert werden.",
         """
/*
 * Task 6: Woerter zaehlen
 * Beschreibung: Dieses Programm liest einen Satz ein und bestimmt die Anzahl der
 * enthaltenen Wörter. Mehrfache Leerzeichen werden ignoriert.
 */
#include <stdio.h>
#include <ctype.h>

int main(void) {
    char s[512];
    printf("Satz: ");
    if (!fgets(s, sizeof(s), stdin)) {
        return 0;
    }
    int in_word = 0;
    int count = 0;
    for (size_t i = 0; s[i] != '\0'; i++) {
        if (isspace((unsigned char)s[i])) {
            in_word = 0;
        } else if (!in_word) {
            in_word = 1;
            count++;
        }
    }
    printf("Anzahl der Woerter: %d\n", count);
    return 0;
}
"""
)

add_task(7, "String umkehren",
         "Kehre einen String um, ohne zusätzliches Array zu verwenden (in-place). Beispiel: 'Programm' → 'mmargorP'.",
         """
/*
 * Task 7: String umkehren
 * Beschreibung: Dieses Programm liest einen String ein und kehrt ihn in-place um.
 */
#include <stdio.h>
#include <string.h>

int main(void) {
    char s[256];
    printf("String: ");
    if (!fgets(s, sizeof(s), stdin)) {
        return 0;
    }
    size_t len = strlen(s);
    if (len > 0 && s[len - 1] == '\n') len--;
    for (size_t i = 0; i < len / 2; i++) {
        char tmp = s[i];
        s[i] = s[len - 1 - i];
        s[len - 1 - i] = tmp;
    }
    s[len] = '\0';
    printf("Umgekehrt: %s\n", s);
    return 0;
}
"""
)

add_task(8, "Anagramm-Checker",
         "Prüfe, ob zwei eingegebene Wörter Anagramme sind (gleiche Buchstaben, andere Reihenfolge).",
         """
/*
 * Task 8: Anagramm-Checker
 * Beschreibung: Dieses Programm prüft, ob zwei eingegebene Wörter Anagramme sind.
 */
#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>

// Vergleichsfunktion für qsort
static int cmp_char(const void *a, const void *b) {
    return (*(const char *)a - *(const char *)b);
}

int main(void) {
    char a[128], b[128];
    printf("Wort 1: ");
    if (!fgets(a, sizeof(a), stdin)) return 0;
    printf("Wort 2: ");
    if (!fgets(b, sizeof(b), stdin)) return 0;
    // Newlines entfernen
    a[strcspn(a, "\n")] = '\0';
    b[strcspn(b, "\n")] = '\0';
    // Nur Buchstaben extrahieren und in Kleinbuchstaben umwandeln
    char s1[128], s2[128];
    int n1 = 0, n2 = 0;
    for (int i = 0; a[i] != '\0'; i++) {
        if (isalpha((unsigned char)a[i])) {
            s1[n1++] = (char)tolower((unsigned char)a[i]);
        }
    }
    for (int i = 0; b[i] != '\0'; i++) {
        if (isalpha((unsigned char)b[i])) {
            s2[n2++] = (char)tolower((unsigned char)b[i]);
        }
    }
    s1[n1] = '\0';
    s2[n2] = '\0';
    if (n1 != n2) {
        printf("Keine Anagramme.\n");
        return 0;
    }
    qsort(s1, n1, sizeof(char), cmp_char);
    qsort(s2, n2, sizeof(char), cmp_char);
    if (strcmp(s1, s2) == 0) {
        printf("Anagramme!\n");
    } else {
        printf("Keine Anagramme.\n");
    }
    return 0;
}
"""
)

add_task(9, "Vokal-Zähler",
         "Zähle alle Vokale (a, e, i, o, u) in einem Text. Bonus: Unterscheide zwischen Groß- und Kleinschreibung.",
         """
/*
 * Task 9: Vokal-Zaehler
 * Beschreibung: Dieses Programm zählt die Vorkommen der Vokale a, e, i, o, u
 * (sowohl klein als auch gross) in einem eingegebenen Text.
 */
#include <stdio.h>
#include <ctype.h>

int main(void) {
    char s[512];
    printf("Text: ");
    if (!fgets(s, sizeof(s), stdin)) {
        return 0;
    }
    int counts[10] = {0};
    // Index 0-4: a, e, i, o, u (klein); 5-9: A, E, I, O, U
    for (size_t i = 0; s[i] != '\0'; i++) {
        switch (s[i]) {
            case 'a': counts[0]++; break;
            case 'e': counts[1]++; break;
            case 'i': counts[2]++; break;
            case 'o': counts[3]++; break;
            case 'u': counts[4]++; break;
            case 'A': counts[5]++; break;
            case 'E': counts[6]++; break;
            case 'I': counts[7]++; break;
            case 'O': counts[8]++; break;
            case 'U': counts[9]++; break;
        }
    }
    int sum = 0;
    for (int i = 0; i < 10; i++) sum += counts[i];
    printf("a:%d e:%d i:%d o:%d u:%d | A:%d E:%d I:%d O:%d U:%d | Summe:%d\n",
           counts[0], counts[1], counts[2], counts[3], counts[4],
           counts[5], counts[6], counts[7], counts[8], counts[9], sum);
    return 0;
}
"""
)

add_task(10, "Längstes Wort im Satz finden",
         "Finde das längste Wort in einem Satz und gib es aus. Beispiel: 'Die Programmierung macht Spaß' → 'Programmierung'.",
         """
/*
 * Task 10: Laengstes Wort im Satz finden
 * Beschreibung: Dieses Programm findet das laengste Wort in einem eingegebenen Satz.
 */
#include <stdio.h>
#include <string.h>
#include <ctype.h>

int main(void) {
    char s[512];
    printf("Satz: ");
    if (!fgets(s, sizeof(s), stdin)) {
        return 0;
    }
    // Entferne das Newline am Ende
    s[strcspn(s, "\n")] = '\0';
    char best[256] = "";
    size_t bestlen = 0;
    char *token = strtok(s, " \t");
    while (token) {
        size_t len = strlen(token);
        if (len > bestlen) {
            bestlen = len;
            strncpy(best, token, sizeof(best) - 1);
            best[sizeof(best) - 1] = '\0';
        }
        token = strtok(NULL, " \t");
    }
    if (bestlen > 0) {
        printf("Laengstes Wort: %s (Laenge %zu)\n", best, bestlen);
    } else {
        printf("Kein Wort gefunden.\n");
    }
    return 0;
}
"""
)

# Level 3 tasks 11–15
add_task(11, "Array sortieren (Bubble Sort)",
          "Implementiere Bubble Sort zum Sortieren eines Arrays. Gib nach jedem Durchlauf den Array-Zustand aus.",
          """
/*
 * Task 11: Bubble Sort
 * Beschreibung: Dieses Programm sortiert ein Array mittels Bubble Sort und
 * zeigt nach jedem Durchlauf den aktuellen Zustand des Arrays.
 */
#include <stdio.h>

static void print_array(const int *a, int n) {
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("\n");
}

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 100) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    int a[100];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    for (int pass = 0; pass < n - 1; pass++) {
        for (int i = 0; i < n - 1 - pass; i++) {
            if (a[i] > a[i + 1]) {
                int tmp = a[i];
                a[i] = a[i + 1];
                a[i + 1] = tmp;
            }
        }
        printf("Nach Durchlauf %d: ", pass + 1);
        print_array(a, n);
    }
    printf("Sortiertes Array: ");
    print_array(a, n);
    return 0;
}
"""
)

add_task(12, "Duplikate entfernen",
          "Entferne alle doppelten Werte aus einem Array. Gib das Array ohne Duplikate aus.",
          """
/*
 * Task 12: Duplikate entfernen
 * Beschreibung: Dieses Programm entfernt alle doppelten Werte aus einem Array
 * und gibt das Ergebnis aus.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    int a[200], b[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int m = 0;
    for (int i = 0; i < n; i++) {
        int ok = 1;
        for (int j = 0; j < m; j++) {
            if (b[j] == a[i]) {
                ok = 0;
                break;
            }
        }
        if (ok) {
            b[m++] = a[i];
        }
    }
    printf("Array ohne Duplikate (%d Elemente): ", m);
    for (int i = 0; i < m; i++) {
        printf("%d ", b[i]);
    }
    printf("\n");
    return 0;
}
"""
)

add_task(13, "Zwei Arrays verschmelzen",
          "Verschmelze zwei sortierte Arrays zu einem sortierten Array.",
          """
/*
 * Task 13: Zwei Arrays verschmelzen
 * Beschreibung: Dieses Programm verschmilzt zwei sortierte Arrays zu
 * einem neuen sortierten Array.
 */
#include <stdio.h>

int main(void) {
    int n, m;
    printf("Laenge des ersten Arrays (n): ");
    if (scanf("%d", &n) != 1 || n < 0 || n > 100) return 0;
    printf("Laenge des zweiten Arrays (m): ");
    if (scanf("%d", &m) != 1 || m < 0 || m > 100) return 0;
    int A[100], B[100], C[205];
    printf("Erstes Array sortiert:\n");
    for (int i = 0; i < n; i++) {
        printf("A[%d] = ", i);
        scanf("%d", &A[i]);
    }
    printf("Zweites Array sortiert:\n");
    for (int j = 0; j < m; j++) {
        printf("B[%d] = ", j);
        scanf("%d", &B[j]);
    }
    int i = 0, j = 0, k = 0;
    while (i < n && j < m) {
        if (A[i] <= B[j]) {
            C[k++] = A[i++];
        } else {
            C[k++] = B[j++];
        }
    }
    while (i < n) C[k++] = A[i++];
    while (j < m) C[k++] = B[j++];
    printf("Verschmolzenes Array: ");
    for (int t = 0; t < k; t++) printf("%d ", C[t]);
    printf("\n");
    return 0;
}
"""
)

add_task(14, "Array rotieren",
          "Rotiere ein Array um K Positionen nach rechts. Beispiel: [1, 2, 3, 4, 5] um 2 rotiert → [4, 5, 1, 2, 3].",
          """
/*
 * Task 14: Array rotieren
 * Beschreibung: Dieses Programm rotiert ein Array um K Positionen nach rechts.
 */
#include <stdio.h>

// Hilfsfunktion, um ein Array in-place zu reversen
static void reverse(int *a, int start, int end) {
    while (start < end) {
        int tmp = a[start];
        a[start] = a[end];
        a[end] = tmp;
        start++;
        end--;
    }
}

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int k;
    printf("K (Anzahl der Positionen): ");
    if (scanf("%d", &k) != 1) return 0;
    k = ((k % n) + n) % n; // Normalisieren fuer negative Werte
    // Dreifach-Reversal: gesamtes Array, erstes Segment, zweites Segment
    reverse(a, 0, n - 1);
    reverse(a, 0, k - 1);
    reverse(a, k, n - 1);
    printf("Rotiertes Array: ");
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("\n");
    return 0;
}
"""
)

add_task(15, "Zweithöchstes Element finden",
          "Finde das zweithöchste Element in einem Array (ohne Sortierung). Hinweis: Merke dir Maximum und Zweitmaximum gleichzeitig.",
          """
/*
 * Task 15: Zweithoechstes Element finden
 * Beschreibung: Dieses Programm findet das zweithöchste Element in einem Array,
 * ohne das Array zu sortieren. Negative Zahlen werden unterstützt.
 */
#include <stdio.h>
#include <limits.h>

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n < 2 || n > 200) {
        printf("Mindestens zwei Elemente sind erforderlich.\n");
        return 0;
    }
    int max1 = INT_MIN, max2 = INT_MIN;
    for (int i = 0; i < n; i++) {
        int x;
        printf("a[%d] = ", i);
        scanf("%d", &x);
        if (x > max1) {
            max2 = max1;
            max1 = x;
        } else if (x > max2 && x < max1) {
            max2 = x;
        }
    }
    if (max2 == INT_MIN) {
        printf("Kein eindeutiges Zweitmaximum gefunden (alle Werte gleich?).\n");
    } else {
        printf("Zweithoechstes Element: %d (Max: %d)\n", max2, max1);
    }
    return 0;
}
"""
)

# Level 4 tasks 16–20
add_task(16, "Matrix transponieren",
          "Transponiere eine NxM Matrix (Zeilen ↔ Spalten tauschen).",
          """
/*
 * Task 16: Matrix transponieren
 * Beschreibung: Dieses Programm liest eine Matrix mit N Zeilen und M Spalten ein
 * und gibt ihre Transponierte aus.
 */
#include <stdio.h>

int main(void) {
    int n, m;
    printf("Anzahl der Zeilen (n): ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 10) return 0;
    printf("Anzahl der Spalten (m): ");
    if (scanf("%d", &m) != 1 || m <= 0 || m > 10) return 0;
    int a[10][10];
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < m; j++) {
            printf("a[%d][%d] = ", i, j);
            scanf("%d", &a[i][j]);
        }
    }
    printf("Transponierte Matrix:\n");
    for (int j = 0; j < m; j++) {
        for (int i = 0; i < n; i++) {
            printf("%d ", a[i][j]);
        }
        printf("\n");
    }
    return 0;
}
"""
)

add_task(17, "Matrix-Multiplikation",
          "Multipliziere zwei Matrizen miteinander. Prüfe, ob die Multiplikation möglich ist (A[m×n] × B[n×p] = C[m×p]).",
          """
/*
 * Task 17: Matrix-Multiplikation
 * Beschreibung: Dieses Programm multipliziert zwei Matrizen A (m×n) und
 * B (n×p) zu einer Ergebnis-Matrix C (m×p). Es wird überprüft, ob die
 * Multiplikation möglich ist.
 */
#include <stdio.h>

int main(void) {
    int m, n, p;
    printf("Dimensionen der Matrix A (m n): ");
    if (scanf("%d %d", &m, &n) != 2 || m <= 0 || n <= 0 || m > 5 || n > 5) return 0;
    printf("Dimensionen der Matrix B (n p): ");
    int n2;
    if (scanf("%d %d", &n2, &p) != 2 || p <= 0 || n2 != n || p > 5) return 0;
    int A[5][5], B[5][5], C[5][5];
    // Matrix A einlesen
    printf("Matrix A:\n");
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < n; j++) {
            printf("A[%d][%d] = ", i, j);
            scanf("%d", &A[i][j]);
        }
    }
    // Matrix B einlesen
    printf("Matrix B:\n");
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < p; j++) {
            printf("B[%d][%d] = ", i, j);
            scanf("%d", &B[i][j]);
        }
    }
    // Matrix C berechnen
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < p; j++) {
            int sum = 0;
            for (int k = 0; k < n; k++) {
                sum += A[i][k] * B[k][j];
            }
            C[i][j] = sum;
        }
    }
    printf("Produktmatrix C:\n");
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < p; j++) {
            printf("%d ", C[i][j]);
        }
        printf("\n");
    }
    return 0;
}
"""
)

add_task(18, "Spirale durch Matrix",
          "Gib eine Matrix in Spiralform aus (außen nach innen).",
          """
/*
 * Task 18: Spirale durch Matrix
 * Beschreibung: Dieses Programm gibt die Elemente einer quadratischen Matrix
 * in einer Spirale aus (im Uhrzeigersinn von außen nach innen).
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Groesse der quadratischen Matrix (n): ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 10) return 0;
    int a[10][10];
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n; j++) {
            printf("a[%d][%d] = ", i, j);
            scanf("%d", &a[i][j]);
        }
    }
    int top = 0, bottom = n - 1, left = 0, right = n - 1;
    printf("Spirale: ");
    while (top <= bottom && left <= right) {
        for (int j = left; j <= right; j++) printf("%d ", a[top][j]);
        top++;
        for (int i = top; i <= bottom; i++) printf("%d ", a[i][right]);
        right--;
        if (top <= bottom) {
            for (int j = right; j >= left; j--) printf("%d ", a[bottom][j]);
            bottom--;
        }
        if (left <= right) {
            for (int i = bottom; i >= top; i--) printf("%d ", a[i][left]);
            left++;
        }
    }
    printf("\n");
    return 0;
}
"""
)

add_task(19, "Diagonalsummen",
          "Berechne die Summen beider Diagonalen einer quadratischen Matrix.",
          """
/*
 * Task 19: Diagonalsummen
 * Beschreibung: Dieses Programm liest eine quadratische Matrix ein und
 * berechnet die Summen der Haupt- und Nebendiagonale.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Groesse der quadratischen Matrix (n): ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 10) return 0;
    int a[10][10];
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n; j++) {
            printf("a[%d][%d] = ", i, j);
            scanf("%d", &a[i][j]);
        }
    }
    int sum_main = 0, sum_sec = 0;
    for (int i = 0; i < n; i++) {
        sum_main += a[i][i];
        sum_sec += a[i][n - 1 - i];
    }
    printf("Summe Hauptdiagonale: %d\n", sum_main);
    printf("Summe Nebendiagonale: %d\n", sum_sec);
    return 0;
}
"""
)

add_task(20, "Magisches Quadrat prüfen",
          "Prüfe, ob eine 3×3 Matrix ein magisches Quadrat ist (alle Summen gleich).",
          """
/*
 * Task 20: Magisches Quadrat pruefen
 * Beschreibung: Dieses Programm prüft, ob eine 3×3 Matrix ein magisches
 * Quadrat ist, d.h. ob die Summen aller Zeilen, Spalten und beider
 * Diagonalen gleich sind.
 */
#include <stdio.h>

int main(void) {
    int a[3][3];
    printf("Geben Sie die 3x3 Matrix ein:\n");
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            printf("a[%d][%d] = ", i, j);
            scanf("%d", &a[i][j]);
        }
    }
    int sum = a[0][0] + a[0][1] + a[0][2];
    int ok = 1;
    // Zeilen prüfen
    for (int i = 1; i < 3 && ok; i++) {
        int row_sum = a[i][0] + a[i][1] + a[i][2];
        if (row_sum != sum) ok = 0;
    }
    // Spalten prüfen
    for (int j = 0; j < 3 && ok; j++) {
        int col_sum = a[0][j] + a[1][j] + a[2][j];
        if (col_sum != sum) ok = 0;
    }
    // Diagonalen prüfen
    if (ok) {
        int d1 = a[0][0] + a[1][1] + a[2][2];
        int d2 = a[0][2] + a[1][1] + a[2][0];
        if (d1 != sum || d2 != sum) ok = 0;
    }
    printf("Die Matrix ist %s ein magisches Quadrat.\n", ok ? "" : "kein");
    return 0;
}
"""
)

# Level 5 tasks 21–25
add_task(21, "Fakultät rekursiv",
          "Berechne die Fakultät einer Zahl mit Rekursion. Beispiel: 5! = 120.",
          """
/*
 * Task 21: Fakultaet rekursiv
 * Beschreibung: Berechnet die Fakultaet einer nichtnegativen Zahl rekursiv.
 */
#include <stdio.h>

static long long factorial(int n) {
    return (n <= 1) ? 1 : n * factorial(n - 1);
}

int main(void) {
    int n;
    printf("n? ");
    if (scanf("%d", &n) != 1 || n < 0) {
        printf("Ungueltige Eingabe.\n");
        return 0;
    }
    printf("%d! = %lld\n", n, factorial(n));
    return 0;
}
"""
)

add_task(22, "GGT (Größter gemeinsamer Teiler)",
          "Berechne den GGT zweier Zahlen mit dem Euklidischen Algorithmus (rekursiv). Beispiel: GGT(48, 18) = 6.",
          """
/*
 * Task 22: Groesster gemeinsamer Teiler
 * Beschreibung: Berechnet den groessten gemeinsamen Teiler zweier Zahlen
 * mittels rekursivem Euklidischen Algorithmus.
 */
#include <stdio.h>

static int ggt(int a, int b) {
    return (b == 0) ? (a >= 0 ? a : -a) : ggt(b, a % b);
}

int main(void) {
    int a, b;
    printf("Zwei Zahlen (a b): ");
    if (scanf("%d %d", &a, &b) != 2) return 0;
    printf("GGT(%d, %d) = %d\n", a, b, ggt(a, b));
    return 0;
}
"""
)

add_task(23, "Türme von Hanoi",
          "Löse das klassische Problem der Türme von Hanoi rekursiv. Bewege N Scheiben von A nach C über B.",
          """
/*
 * Task 23: Tuerme von Hanoi
 * Beschreibung: Dieses Programm loest das Tuerme-von-Hanoi-Problem rekursiv
 * und gibt die benoetigten Schritte aus.
 */
#include <stdio.h>

static void hanoi(int n, char from, char to, char aux) {
    if (n == 0) return;
    hanoi(n - 1, from, aux, to);
    printf("Bewege Scheibe %d von %c nach %c\n", n, from, to);
    hanoi(n - 1, aux, to, from);
}

int main(void) {
    int n;
    printf("Anzahl der Scheiben: ");
    if (scanf("%d", &n) != 1 || n < 0) return 0;
    hanoi(n, 'A', 'C', 'B');
    return 0;
}
"""
)

add_task(24, "Binäre Suche (rekursiv)",
          "Implementiere binäre Suche in einem sortierten Array mit Rekursion.",
          """
/*
 * Task 24: Binaere Suche rekursiv
 * Beschreibung: Dieses Programm demonstriert die rekursive binäre Suche in
 * einem sortierten Array. Das Array muss vorab sortiert sein.
 */
#include <stdio.h>

static int binary_search(const int *a, int left, int right, int target) {
    if (left > right) return -1;
    int mid = left + (right - left) / 2;
    if (a[mid] == target) return mid;
    if (a[mid] > target) return binary_search(a, left, mid - 1, target);
    return binary_search(a, mid + 1, right, target);
}

int main(void) {
    int n;
    printf("Laenge des sortierten Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 100) return 0;
    int a[100];
    printf("Geben Sie %d sortierte Werte ein:\n", n);
    for (int i = 0; i < n; i++) {
        scanf("%d", &a[i]);
    }
    int target;
    printf("Zielwert: ");
    scanf("%d", &target);
    int pos = binary_search(a, 0, n - 1, target);
    if (pos >= 0) {
        printf("Wert %d gefunden an Position %d.\n", target, pos);
    } else {
        printf("Wert %d nicht gefunden.\n", target);
    }
    return 0;
}
"""
)

add_task(25, "Potenz berechnen (schnell)",
          "Berechne x^n effizient mit 'Fast Exponentiation' (rekursiv). Wenn n gerade: x^n = (x^(n/2))^2, sonst: x * x^(n-1).",
          """
/*
 * Task 25: Potenz berechnen (schnell)
 * Beschreibung: Berechnet x^n effizient mit Schnell-Exponentiation.
 */
#include <stdio.h>

static long long fast_pow(long long x, long long n) {
    if (n == 0) return 1;
    if (n % 2 == 0) {
        long long half = fast_pow(x, n / 2);
        return half * half;
    }
    return x * fast_pow(x, n - 1);
}

int main(void) {
    long long x, n;
    printf("Basis x und Exponent n: ");
    if (scanf("%lld %lld", &x, &n) != 2) return 0;
    long long result = fast_pow(x, n);
    printf("%lld^%lld = %lld\n", x, n, result);
    return 0;
}
"""
)

# Level 6 tasks 26–30
add_task(26, "Swap mit Pointern",
          "Schreibe eine Funktion, die zwei Variablen mit Pointern vertauscht.",
          """
/*
 * Task 26: Swap mit Pointern
 * Beschreibung: Dieses Programm demonstriert das Tauschen zweier Werte mittels
 * Zeigern.
 */
#include <stdio.h>

static void swap(int *a, int *b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

int main(void) {
    int x, y;
    printf("Zahl 1: ");
    scanf("%d", &x);
    printf("Zahl 2: ");
    scanf("%d", &y);
    printf("Vor dem Tausch: x=%d y=%d\n", x, y);
    swap(&x, &y);
    printf("Nach dem Tausch: x=%d y=%d\n", x, y);
    return 0;
}
"""
)

add_task(27, "Array-Summe mit Pointer",
          "Berechne die Summe eines Arrays nur mit Pointer-Arithmetik (kein Index).",
          """
/*
 * Task 27: Array-Summe mit Pointer
 * Beschreibung: Dieses Programm berechnet die Summe der Elemente eines Arrays
 * ausschliesslich mit Pointer-Arithmetik.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int sum = 0;
    int *ptr = a;
    for (int i = 0; i < n; i++) {
        sum += *(ptr++);
    }
    printf("Summe = %d\n", sum);
    return 0;
}
"""
)

add_task(28, "String-Länge ohne strlen()",
          "Implementiere eine eigene strlen()-Funktion mit Pointern.",
          """
/*
 * Task 28: String-Laenge ohne strlen()
 * Beschreibung: Dieses Programm implementiert eine eigene Funktion zur Bestimmung
 * der Laenge eines Strings mittels Pointer-Arithmetik.
 */
#include <stdio.h>

static size_t my_strlen(const char *s) {
    const char *p = s;
    while (*p) p++;
    return (size_t)(p - s);
}

int main(void) {
    char s[256];
    printf("String: ");
    if (!fgets(s, sizeof(s), stdin)) return 0;
    // Entferne newline
    s[strcspn(s, "\n")] = '\0';
    printf("Laenge = %zu\n", my_strlen(s));
    return 0;
}
"""
)

add_task(29, "Array umkehren mit Pointern",
          "Kehre ein Array mit zwei Pointern um (ohne zusätzlichen Speicher).",
          """
/*
 * Task 29: Array umkehren mit Pointern
 * Beschreibung: Dieses Programm kehrt ein Array in-place um, indem zwei
 * Zeiger verwendet werden, die sich aufeinander zubewegen.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int *left = &a[0];
    int *right = &a[n - 1];
    while (left < right) {
        int tmp = *left;
        *left = *right;
        *right = tmp;
        left++;
        right--;
    }
    printf("Umgekehrtes Array: ");
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("\n");
    return 0;
}
"""
)

add_task(30, "Dynamisches Array",
          "Erstelle ein dynamisches Array mit malloc(), füge Elemente hinzu und gib den Speicher frei.",
          """
/*
 * Task 30: Dynamisches Array
 * Beschreibung: Dieses Programm demonstriert die Verwendung von malloc(),
 * um ein Array dynamisch zu erzeugen, Elemente einzulesen und am Ende
 * den Speicher wieder freizugeben.
 */
#include <stdio.h>
#include <stdlib.h>

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0) return 0;
    int *arr = malloc((size_t)n * sizeof(int));
    if (!arr) {
        printf("Speicherreservierung fehlgeschlagen.\n");
        return 0;
    }
    for (int i = 0; i < n; i++) {
        printf("arr[%d] = ", i);
        scanf("%d", &arr[i]);
    }
    printf("Eingegebene Elemente: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", arr[i]);
    }
    printf("\n");
    free(arr);
    return 0;
}
"""
)

# Level 7 tasks 31–35
add_task(31, "Studenten-Verwaltung",
          "Erstelle eine Struktur Student mit Name, Matrikelnummer und Noten. Verwalte mehrere Studenten, berechne die Durchschnittsnote und finde den besten Studenten.",
          """
/*
 * Task 31: Studenten-Verwaltung
 * Beschreibung: Dieses Programm definiert eine Struktur fuer Studenten und
 * erlaubt die Verwaltung mehrerer Studenten, einschliesslich der Berechnung
 * der Durchschnittsnoten und dem Finden des besten Studenten.
 */
#include <stdio.h>
#include <string.h>

typedef struct {
    char name[50];
    int matrikel;
    float grades[10];
    int grade_count;
} Student;

static float average(const Student *s) {
    if (s->grade_count == 0) return 0.0f;
    float sum = 0.0f;
    for (int i = 0; i < s->grade_count; i++) sum += s->grades[i];
    return sum / s->grade_count;
}

int main(void) {
    int n;
    printf("Anzahl der Studenten: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 50) return 0;
    Student students[50];
    for (int i = 0; i < n; i++) {
        printf("Student %d Name: ", i + 1);
        scanf("%s", students[i].name);
        printf("Matrikelnummer: ");
        scanf("%d", &students[i].matrikel);
        printf("Anzahl der Noten: ");
        scanf("%d", &students[i].grade_count);
        for (int j = 0; j < students[i].grade_count; j++) {
            printf("Note %d: ", j + 1);
            scanf("%f", &students[i].grades[j]);
        }
    }
    // Durchschnitt berechnen und besten Studenten finden
    int best_index = 0;
    float best_avg = average(&students[0]);
    for (int i = 1; i < n; i++) {
        float avg = average(&students[i]);
        if (avg > best_avg) {
            best_avg = avg;
            best_index = i;
        }
    }
    printf("Bester Student: %s (Matrikel %d) mit Durchschnitt %.2f\n",
           students[best_index].name, students[best_index].matrikel, best_avg);
    return 0;
}
"""
)

add_task(32, "Rechteck vs. Kreis",
          "Erstelle Strukturen für Rechteck und Kreis. Berechne Fläche und Umfang. Vergleiche, welche Figur die größere Fläche hat.",
          """
/*
 * Task 32: Rechteck vs. Kreis
 * Beschreibung: Dieses Programm vergleicht die Flaechen von Rechteck und Kreis
 * anhand benutzerdefinierter Eingaben.
 */
#include <stdio.h>
#include <math.h>

typedef struct {
    double breite;
    double hoehe;
} Rechteck;

typedef struct {
    double radius;
} Kreis;

static double area_rect(const Rechteck *r) {
    return r->breite * r->hoehe;
}

static double area_circle(const Kreis *c) {
    return M_PI * c->radius * c->radius;
}

int main(void) {
    Rechteck r;
    Kreis c;
    printf("Rechteck Breite und Hoehe: ");
    scanf("%lf %lf", &r.breite, &r.hoehe);
    printf("Kreis Radius: ");
    scanf("%lf", &c.radius);
    double area_r = area_rect(&r);
    double area_c = area_circle(&c);
    printf("Flaeche Rechteck: %.2f\n", area_r);
    printf("Flaeche Kreis: %.2f\n", area_c);
    if (area_r > area_c) printf("Das Rechteck hat die groessere Flaeche.\n");
    else if (area_c > area_r) printf("Der Kreis hat die groessere Flaeche.\n");
    else printf("Beide Figuren haben die gleiche Flaeche.\n");
    return 0;
}
"""
)

add_task(33, "Linked List (verkettete Liste)",
          "Implementiere eine einfache verkettete Liste mit struct Node. Operationen: Einfügen, Löschen, Ausgeben.",
          """
/*
 * Task 33: Linked List
 * Beschreibung: Dieses Programm implementiert eine einfache singly linked list
 * mit Einfuegen am Ende, Loeschen nach Wert und Ausgeben der Liste.
 */
#include <stdio.h>
#include <stdlib.h>

typedef struct Node {
    int data;
    struct Node *next;
} Node;

static void print_list(const Node *head) {
    const Node *cur = head;
    while (cur) {
        printf("%d -> ", cur->data);
        cur = cur->next;
    }
    printf("NULL\n");
}

static void push_back(Node **head, int value) {
    Node *new_node = malloc(sizeof(Node));
    new_node->data = value;
    new_node->next = NULL;
    if (!*head) {
        *head = new_node;
        return;
    }
    Node *cur = *head;
    while (cur->next) cur = cur->next;
    cur->next = new_node;
}

static void delete_value(Node **head, int value) {
    Node *cur = *head, *prev = NULL;
    while (cur) {
        if (cur->data == value) {
            if (prev) prev->next = cur->next;
            else *head = cur->next;
            free(cur);
            return;
        }
        prev = cur;
        cur = cur->next;
    }
}

int main(void) {
    Node *head = NULL;
    int choice;
    while (1) {
        printf("1: Einfuegen | 2: Loeschen | 3: Ausgeben | 0: Ende → ");
        if (scanf("%d", &choice) != 1) break;
        if (choice == 0) break;
        if (choice == 1) {
            int value;
            printf("Wert einfuegen: ");
            scanf("%d", &value);
            push_back(&head, value);
        } else if (choice == 2) {
            int value;
            printf("Wert loeschen: ");
            scanf("%d", &value);
            delete_value(&head, value);
        } else if (choice == 3) {
            print_list(head);
        }
    }
    // Liste freigeben
    Node *cur = head;
    while (cur) {
        Node *next = cur->next;
        free(cur);
        cur = next;
    }
    return 0;
}
"""
)

add_task(34, "Bibliothekssystem",
          "Erstelle ein Mini-Bibliothekssystem mit Büchern (Titel, Autor, ISBN, Status). Features: Buch ausleihen/zurückgeben, alle Bücher anzeigen, nach Titel suchen.",
          """
/*
 * Task 34: Bibliothekssystem
 * Beschreibung: Dieses Programm implementiert ein einfaches Bibliothekssystem
 * zur Verwaltung von Buechern. Es erlaubt das Hinzufuegen von Buechern, die
 * Anzeige aller Buecher, das Ausleihen und Zurueckgeben sowie die Suche
 * nach Titeln. Zum Zwecke der Übersicht wird eine feste Obergrenze von
 * 100 Buechern gesetzt.
 */
#include <stdio.h>
#include <string.h>

typedef struct {
    char titel[100];
    char autor[100];
    char isbn[20];
    int entliehen; // 0 = verfuegbar, 1 = entliehen
} Buch;

static void add_book(Buch *buecher, int *count) {
    if (*count >= 100) {
        printf("Bibliothek ist voll!\n");
        return;
    }
    Buch *b = &buecher[*count];
    printf("Titel: ");
    scanf("%99s", b->titel);
    printf("Autor: ");
    scanf("%99s", b->autor);
    printf("ISBN: ");
    scanf("%19s", b->isbn);
    b->entliehen = 0;
    (*count)++;
}

static void list_books(const Buch *buecher, int count) {
    for (int i = 0; i < count; i++) {
        printf("%d: %s von %s (ISBN: %s) – %s\n", i, buecher[i].titel, buecher[i].autor,
               buecher[i].isbn, buecher[i].entliehen ? "entliehen" : "verfuegbar");
    }
}

static void lend_book(Buch *buecher, int count) {
    int idx;
    printf("Index des Buches ausleihen: ");
    scanf("%d", &idx);
    if (idx < 0 || idx >= count) {
        printf("Ungueltiger Index!\n");
        return;
    }
    if (buecher[idx].entliehen) {
        printf("Buch ist bereits entliehen.\n");
    } else {
        buecher[idx].entliehen = 1;
        printf("Buch ausgeliehen.\n");
    }
}

static void return_book(Buch *buecher, int count) {
    int idx;
    printf("Index des Buches zurueckgeben: ");
    scanf("%d", &idx);
    if (idx < 0 || idx >= count) {
        printf("Ungueltiger Index!\n");
        return;
    }
    if (!buecher[idx].entliehen) {
        printf("Buch ist nicht entliehen.\n");
    } else {
        buecher[idx].entliehen = 0;
        printf("Buch zurueckgegeben.\n");
    }
}

static void search_title(const Buch *buecher, int count) {
    char query[100];
    printf("Titel suchen: ");
    scanf("%99s", query);
    for (int i = 0; i < count; i++) {
        if (strstr(buecher[i].titel, query)) {
            printf("Gefunden: %d: %s von %s (ISBN: %s) – %s\n", i, buecher[i].titel, buecher[i].autor,
                   buecher[i].isbn, buecher[i].entliehen ? "entliehen" : "verfuegbar");
        }
    }
}

int main(void) {
    Buch buecher[100];
    int count = 0;
    int choice;
    while (1) {
        printf("1: Hinzufuegen | 2: Anzeigen | 3: Ausleihen | 4: Zurueckgeben | 5: Suchen | 0: Ende → ");
        if (scanf("%d", &choice) != 1) break;
        if (choice == 0) break;
        if (choice == 1) add_book(buecher, &count);
        else if (choice == 2) list_books(buecher, count);
        else if (choice == 3) lend_book(buecher, count);
        else if (choice == 4) return_book(buecher, count);
        else if (choice == 5) search_title(buecher, count);
    }
    return 0;
}
"""
)

add_task(35, "Zeitrechner",
          "Erstelle eine Struktur Zeit mit Stunden, Minuten, Sekunden. Funktionen: zwei Zeiten addieren, Zeitdifferenz berechnen, Zeit formatiert ausgeben. Beachte Überläufe (60 Sekunden = 1 Minute).",
          """
/*
 * Task 35: Zeitrechner
 * Beschreibung: Dieses Programm definiert eine Struktur fuer Zeiten und
 * implementiert Funktionen zum Addieren und Subtrahieren zweier Zeiten.
 */
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    int h, m, s;
} Zeit;

static void print_time(const Zeit *t) {
    printf("%02d:%02d:%02d", t->h, t->m, t->s);
}

static Zeit add_times(Zeit a, Zeit b) {
    Zeit res;
    res.s = a.s + b.s;
    res.m = a.m + b.m + res.s / 60;
    res.h = a.h + b.h + res.m / 60;
    res.s %= 60;
    res.m %= 60;
    return res;
}

static Zeit diff_times(Zeit a, Zeit b) {
    // Berechne a - b (als Absolutwert)
    Zeit res;
    int sec_a = a.h * 3600 + a.m * 60 + a.s;
    int sec_b = b.h * 3600 + b.m * 60 + b.s;
    int diff = abs(sec_a - sec_b);
    res.h = diff / 3600;
    res.m = (diff % 3600) / 60;
    res.s = diff % 60;
    return res;
}

int main(void) {
    Zeit t1, t2;
    printf("Zeit 1 (h m s): ");
    scanf("%d %d %d", &t1.h, &t1.m, &t1.s);
    printf("Zeit 2 (h m s): ");
    scanf("%d %d %d", &t2.h, &t2.m, &t2.s);
    Zeit sum = add_times(t1, t2);
    Zeit diff = diff_times(t1, t2);
    printf("Summe: ");
    print_time(&sum);
    printf("\nDifferenz: ");
    print_time(&diff);
    printf("\n");
    return 0;
}
"""
)

# Remaining tasks 36–135 skeleton
remaining_tasks = [
    (36, "Textdatei zeilenweise einlesen", "Lies eine Textdatei ein und gib sie nummeriert aus."),
    (37, "Wörter in Datei zählen", "Zähle Zeilen, Wörter und Zeichen in einer Textdatei."),
    (38, "Noten-Statistik aus Datei", "Lies Noten aus einer Datei und berechne Durchschnitt, Beste und Schlechteste."),
    (39, "CSV-Parser", "Parse eine einfache CSV-Datei und gib sie tabellarisch aus."),
    (40, "Log-Datei schreiben", "Schreibe Aktivitäten mit Zeitstempel in eine Log-Datei."),
    (41, "Taschenrechner mit Menü", "Erstelle einen Taschenrechner mit Menü (Grundrechenarten, Potenz, Wurzel)."),
    (42, "Tic-Tac-Toe", "Programmiere Tic-Tac-Toe für zwei Spieler."),
    (43, "Kontaktbuch", "Erstelle ein Kontaktbuch mit Name, Telefon und E-Mail."),
    (44, "Hangman", "Implementiere das Wort-Rate-Spiel Hangman."),
    (45, "Snake-Spielfeld", "Simuliere ein einfaches Snake-Spiel im Terminal."),
    (46, "Selection Sort", "Implementiere Selection Sort und visualisiere jeden Schritt."),
    (47, "Insertion Sort", "Implementiere Insertion Sort."),
    (48, "Linear Search vs. Binary Search", "Implementiere beide Suchalgorithmen und vergleiche die Laufzeit."),
    (49, "Merge Sort", "Implementiere den rekursiven Merge Sort Algorithmus."),
    (50, "Quick Sort", "Implementiere Quick Sort mit Wahl eines Pivotelements."),
    (51, "Sudoku-Löser", "Schreibe ein Programm, das ein 9×9 Sudoku löst (Backtracking)."),
    (52, "Huffman-Kodierung", "Implementiere Huffman-Kodierung zur Datenkompression."),
    (53, "A*-Pathfinding", "Finde den kürzesten Weg in einem Labyrinth mit dem A*-Algorithmus."),
    (54, "Mini-Datenbank", "Erstelle eine einfache Datenbank mit CRUD-Operationen."),
    (55, "Eigener Interpreter", "Schreibe einen Mini-Interpreter für einfache mathematische Ausdrücke."),
    (56, "Bit-Zähler", "Zähle die Anzahl der gesetzten Bits (1en) in einer Zahl."),
    (57, "Potenz von 2 prüfen", "Prüfe mit nur einer Bit-Operation, ob eine Zahl eine Potenz von 2 ist."),
    (58, "Bit-Swap", "Vertausche die Werte zweier Variablen ohne temporäre Variable (nur mit XOR)."),
    (59, "N-tes Bit umschalten", "Schreibe Funktionen: setBit(), clearBit(), toggleBit(), checkBit()."),
    (60, "Byte-Swapping", "Kehre die Byte-Reihenfolge einer 32-Bit Zahl um (Endianness-Konvertierung)."),
    (61, "Gray-Code Generator", "Generiere die ersten N Gray-Code Zahlen."),
    (62, "Einzelnes fehlendes Bit finden", "Finde die fehlende Zahl in einem Array 0–N mittels XOR."),
    (63, "Bitfeld-Struktur", "Erstelle eine Struktur mit Bitfeldern für Flags."),
    (64, "Bit-Rotation", "Implementiere Links- und Rechts-Rotation von Bits."),
    (65, "Bit-Reversal", "Kehre alle Bits einer Zahl um."),
    (66, "Funktionspointer-Taschenrechner", "Implementiere einen Taschenrechner mit Array von Funktionspointern."),
    (67, "Callback-Funktionen", "Implementiere eine forEach()-Funktion für Arrays mit Callback."),
    (68, "Generic Swap mit void*", "Schreibe eine universelle Swap-Funktion mit void*-Zeigern."),
    (69, "Pointer auf Pointer", "Implementiere eine Funktion, die Speicher allokiert und den Pointer ändert."),
    (70, "Function Pointer Table", "Erstelle ein Command-Pattern mit Function Pointer Table."),
    (71, "qsort() verstehen und nutzen", "Nutze qsort() mit eigener Compare-Funktion."),
    (72, "Pointer-Arithmetik Meisterschaft", "Navigiere durch ein 2D-Array nur mit Pointer-Arithmetik."),
    (73, "Memory Pool Allocator", "Implementiere einen einfachen Memory Pool für schnelle Allokationen."),
    (74, "Const Pointer Varianten verstehen", "Demonstriere alle Varianten von const Pointer."),
    (75, "Pointer vs. Array - Deep Dive", "Zeige die Unterschiede zwischen int arr[] und int *ptr."),
    (76, "Stack (LIFO) implementieren", "Implementiere einen Stack mit Array und mit Linked List."),
    (77, "Queue (FIFO) implementieren", "Implementiere eine Queue als Ringpuffer."),
    (78, "Priority Queue", "Implementiere eine Priority Queue mit Binary Heap."),
    (79, "Doubly Linked List", "Erweitere eine Linked List zu einer doppelt verketteten Liste."),
    (80, "Binary Search Tree (BST)", "Implementiere einen binären Suchbaum."),
    (81, "Hash Table", "Implementiere eine Hash Table mit Chaining."),
    (82, "Trie (Prefix Tree)", "Implementiere einen Trie für schnelle String-Suche."),
    (83, "Graph mit Adjacency Matrix", "Repräsentiere einen Graphen mit einer Adjazenzmatrix."),
    (84, "Graph mit Adjacency List", "Repräsentiere einen Graphen mit einer Adjazenzliste."),
    (85, "Union-Find (Disjoint Set)", "Implementiere die Union-Find Datenstruktur."),
    (86, "Dijkstra's Shortest Path", "Finde den kürzesten Pfad in einem gewichteten Graphen."),
    (87, "Floyd-Warshall", "Berechne kürzeste Pfade zwischen allen Knotenpaaren."),
    (88, "Kruskal's MST", "Finde den minimalen Spannbaum mit Kruskal's Algorithmus."),
    (89, "Knapsack Problem (0/1)", "Löse das Rucksackproblem mit Dynamic Programming."),
    (90, "Longest Common Subsequence (LCS)", "Finde die längste gemeinsame Teilsequenz zweier Strings."),
    (91, "Edit Distance (Levenshtein)", "Berechne die Editier-Distanz zwischen zwei Strings."),
    (92, "Rabin-Karp String Matching", "Finde ein Pattern in Text mittels Rolling Hash."),
    (93, "KMP (Knuth-Morris-Pratt)", "Implementiere KMP Pattern Matching."),
    (94, "Boyer-Moore String Search", "Implementiere Boyer-Moore Algorithmus."),
    (95, "Topological Sort", "Sortiere einen DAG topologisch."),
    (96, "Multi-threaded Prime Finder", "Finde Primzahlen parallel mit pthreads."),
    (97, "Producer-Consumer Problem", "Implementiere das Producer-Consumer Problem mit Mutex/Condition."),
    (98, "Reader-Writer Lock", "Implementiere ein Reader-Writer Lock."),
    (99, "Thread Pool", "Implementiere einen einfachen Thread Pool."),
    (100, "Dining Philosophers", "Löse das Dining Philosophers Problem ohne Deadlock."),
    (101, "Eigener malloc()", "Implementiere eine einfache malloc/free Funktion."),
    (102, "Memory Leak Detector", "Schreibe Wrapper für malloc/free, die Leaks tracken."),
    (103, "Reference Counting", "Implementiere Reference Counting für automatisches Memory Management."),
    (104, "Stack vs. Heap Visualizer", "Demonstriere Unterschiede zwischen Stack und Heap."),
    (105, "Memory Alignment", "Demonstriere Memory Alignment und Padding in Strukturen."),
    (106, "Fork & Exec", "Erstelle Child-Prozesse mit fork() und exec()."),
    (107, "Pipes für IPC", "Kommuniziere zwischen Prozessen mit Pipes."),
    (108, "Signal Handling", "Fange Signale ab und reagiere darauf."),
    (109, "Shared Memory (shm)", "Teile Speicher zwischen Prozessen mit shmget/shmat."),
    (110, "Memory-Mapped Files", "Mappe Dateien in Speicher mit mmap()."),
    (111, "Directory Walker", "Durchlaufe Verzeichnisstrukturen rekursiv."),
    (112, "File Permissions Checker", "Prüfe und ändere Dateirechte programmatisch."),
    (113, "Daemon-Prozess", "Erstelle einen Daemon-Prozess im Hintergrund."),
    (114, "Simple HTTP Server", "Implementiere einen minimalen HTTP-Server mit Sockets."),
    (115, "TCP Chat Application", "Erstelle eine Chat-App mit TCP-Sockets."),
    (116, "Lexer (Tokenizer)", "Schreibe einen Lexer für eine Mini-Sprache."),
    (117, "Recursive Descent Parser", "Parse arithmetische Ausdrücke mit Rekursion."),
    (118, "RPN Calculator", "Implementiere einen Postfix-Evaluator."),
    (119, "Infix zu Postfix Converter", "Konvertiere Infix- zu Postfix-Notation."),
    (120, "Mini-Interpreter mit Variablen", "Erweitere den Calculator um Variablen-Zuweisung."),
    (121, "XOR-Verschlüsselung", "Implementiere eine einfache XOR-Cipher."),
    (122, "ROT13", "Implementiere ROT13 (Caesar mit Shift 13)."),
    (123, "Base64 Encoder/Decoder", "Implementiere Base64 Encoding und Decoding."),
    (124, "Einfacher Hash (DJB2)", "Implementiere die DJB2 Hash-Funktion."),
    (125, "MD5 implementieren", "Implementiere den MD5 Algorithmus."),
    (126, "Cache-Friendly Code", "Demonstriere Cache-Effekte bei Matrix-Traversierung."),
    (127, "SIMD mit SSE", "Nutze SIMD-Instruktionen fuer parallele Berechnungen."),
    (128, "Branch Prediction Optimization", "Demonstriere Auswirkungen der Branch Prediction."),
    (129, "Loop Unrolling", "Optimiere Schleifen durch Loop Unrolling."),
    (130, "Profiling mit gprof", "Profile den Code und optimiere Hotspots."),
    (131, "Virtuelle Maschine (VM)", "Implementiere eine Stack-basierte VM."),
    (132, "Garbage Collector", "Implementiere einen einfachen Mark-and-Sweep GC."),
    (133, "JIT Compiler (Basic)", "Generiere x86-Maschinencode zur Laufzeit."),
    (134, "Betriebssystem-Kernel (Mini)", "Schreibe einen minimalen Bootloader und Kernel."),
    (135, "Regex Engine", "Implementiere eine Regex-Engine (Thompson NFA)."),
]

for tid, title, description in remaining_tasks:
    add_task(tid, title, description)

# Write each task file
for task in tasks_data:
    tid = task["id"]
    title = task["title"]
    filename = f"{tid:03d}_{slugify(title)}.c"
    code = task.get("code")
    if code is None:
        code = f"/*\n * Task {tid}: {title}\n * Beschreibung: {task['description']}\n * Diese Aufgabe ist noch nicht implementiert.\n */\n#include <stdio.h>\n\nint main(void) {{\n    // TODO: Implementieren Sie diese Aufgabe.\n    printf(\"Task {tid} ist noch nicht implementiert.\\n\");\n    return 0;\n}}\n"
    with open(TASKS_DIR / filename, "w", encoding="utf-8") as f:
        f.write(code)

# Generate index.html
html_lines = []
html_lines.append("<!DOCTYPE html>")
html_lines.append("<html lang='de'>")
html_lines.append("<head>")
html_lines.append("  <meta charset='UTF-8'>")
html_lines.append("  <meta name='viewport' content='width=device-width, initial-scale=1.0'>")
html_lines.append("  <title>C-Programmierung Portfolio</title>")
html_lines.append("  <link rel='stylesheet' href='style.css'>")
html_lines.append("</head>")
html_lines.append("<body>")
html_lines.append("  <header>")
html_lines.append("    <h1>C-Programmierung Portfolio</h1>")
html_lines.append("    <p>Dies ist ein interaktives Portfolio aller Übungsaufgaben (1–135). "
                 "Über die Suchfunktion können Sie nach Titeln filtern. Aufgaben, die "
                 "implementiert wurden, sind in grün markiert.</p>")
html_lines.append("    <input type='text' id='search' placeholder='Suchen...' oninput='filterTasks()'>")
html_lines.append("  </header>")
html_lines.append("  <main id='tasks-container'>")

# Helper to determine level
def level_of(tid):
    if tid <= 5: return 1
    elif tid <= 10: return 2
    elif tid <= 15: return 3
    elif tid <= 20: return 4
    elif tid <= 25: return 5
    elif tid <= 30: return 6
    elif tid <= 35: return 7
    elif tid <= 40: return 8
    elif tid <= 45: return 9
    elif tid <= 50: return 10
    elif tid <= 55: return 11
    elif tid <= 65: return 12
    elif tid <= 75: return 13
    elif tid <= 85: return 14
    elif tid <= 95: return 15
    elif tid <= 105: return 16
    elif tid <= 115: return 17
    elif tid <= 120: return 18
    elif tid <= 125: return 19
    elif tid <= 130: return 20
    else: return 21

current_level = None
for task in tasks_data:
    level = level_of(task["id"])
    if level != current_level:
        if current_level is not None:
            html_lines.append("    </section>")
        html_lines.append(f"    <section class='level'><h2>Level {level}</h2>")
        current_level = level
    status_class = "completed" if task.get("code") else "todo"
    filename = f"tasks/{task['id']:03d}_{slugify(task['title'])}.c"
    html_lines.append(f"      <article class='task {status_class}' data-title='{task['title'].lower()}'>")
    html_lines.append(f"        <h3>{task['id']}. {task['title']}</h3>")
    html_lines.append(f"        <p>{task['description']}</p>")
    html_lines.append(f"        <a href='{filename}' download>Quellcode herunterladen</a>")
    html_lines.append("      </article>")
if current_level is not None:
    html_lines.append("    </section>")

html_lines.append("  </main>")
html_lines.append("  <script src='script.js'></script>")
html_lines.append("</body>")
html_lines.append("</html>")

(Path(ROOT) / "index.html").write_text("\n".join(html_lines), encoding="utf-8")

# Write CSS
css = """
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
}
header {
    background-color: #333;
    color: white;
    padding: 1rem;
    text-align: center;
}
header h1 {
    margin: 0;
}
header p {
    margin: 0.5rem 0;
}
header input {
    padding: 0.5rem;
    width: 80%;
    max-width: 400px;
    margin-top: 1rem;
    border: none;
    border-radius: 4px;
}
main {
    padding: 1rem;
}
section.level {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #ccc;
}
section.level h2 {
    margin-top: 0;
    color: #444;
}
article.task {
    background-color: white;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
article.task.completed {
    border-left: 6px solid #4CAF50;
}
article.task.todo {
    border-left: 6px solid #F44336;
}
article.task h3 {
    margin-top: 0;
}
article.task a {
    display: inline-block;
    margin-top: 0.5rem;
    color: #2196F3;
    text-decoration: none;
}
article.task a:hover {
    text-decoration: underline;
}
"""
(Path(ROOT) / "style.css").write_text(css, encoding="utf-8")

# Write JS
js = """
function filterTasks() {
  const query = document.getElementById('search').value.toLowerCase();
  const tasks = document.querySelectorAll('article.task');
  tasks.forEach(task => {
    const title = task.getAttribute('data-title');
    if (title.includes(query)) {
      task.style.display = '';
    } else {
      task.style.display = 'none';
    }
  });
}
"""
(Path(ROOT) / "script.js").write_text(js, encoding="utf-8")
