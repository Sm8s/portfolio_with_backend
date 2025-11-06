#include <stdio.h>
#include <stdlib.h>
#include <time.h>

// Einfaches Zahlenraten-Spiel. Der Computer wählt eine Zufallszahl und der Spieler rät.
int main(void) {
    srand((unsigned)time(NULL));
    int secret = rand() % 100 + 1;
    int guess;
    printf("Ich habe eine Zahl zwischen 1 und 100 gewählt. Rate sie!\n");
    do {
        printf("Dein Tipp: ");
        if (scanf("%d", &guess) != 1) {
            printf("Ungültige Eingabe. Bitte eine Zahl eingeben.\n");
            int c; while ((c = getchar()) != '\n' && c != EOF); // Eingabepuffer leeren
            continue;
        }
        if (guess < secret) {
            printf("Zu niedrig!\n");
        } else if (guess > secret) {
            printf("Zu hoch!\n");
        } else {
            printf("Richtig! Die Zahl war %d.\n", secret);
        }
    } while (guess != secret);
    return 0;
}