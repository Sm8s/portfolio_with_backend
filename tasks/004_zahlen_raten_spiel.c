
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
    printf("Ich denke mir eine Zahl zwischen 1 und 100. Versuche sie zu erraten.
");
    while (1) {
        printf("Tipp? ");
        if (scanf("%d", &guess) != 1) {
            printf("Ungueltige Eingabe.
");
            // Eingabepuffer leeren
            int c;
            while ((c = getchar()) != '
' && c != EOF) {}
            continue;
        }
        tries++;
        if (guess < target) {
            printf("Zu niedrig!
");
        } else if (guess > target) {
            printf("Zu hoch!
");
        } else {
            printf("Richtig! Du hast die Zahl in %d Versuchen erraten.
", tries);
            break;
        }
    }
    return 0;
}
