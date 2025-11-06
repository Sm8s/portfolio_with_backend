#include <stdio.h>
#include <string.h>

/*
 * Einfaches Text-Abenteuer (Zork-ähnlich):
 * Der Spieler wird durch Entscheidungen geführt. Dies ist eine kleine Demonstration
 * mit nur wenigen Räumen. Für eine vollständige Version müssen Sie diese Struktur
 * erweitern und ggf. in Dateien auslagern.
 */

void start();
void in_forest();
void in_cave();

int main(void) {
    start();
    return 0;
}

void start() {
    char choice[10];
    printf("Du stehst am Eingang eines Waldes. Willst du hinein gehen? (ja/nein)\n");
    scanf("%9s", choice);
    if (strcmp(choice, "ja") == 0) {
        in_forest();
    } else {
        printf("Du entscheidest dich, nach Hause zu gehen. Ende.\n");
    }
}

void in_forest() {
    char choice[10];
    printf("Du bist jetzt im Wald und siehst einen Höhleneingang. Willst du hinein gehen? (ja/nein)\n");
    scanf("%9s", choice);
    if (strcmp(choice, "ja") == 0) {
        in_cave();
    } else {
        printf("Du wanderst weiter im Wald, aber findest nichts Interessantes. Ende.\n");
    }
}

void in_cave() {
    char choice[10];
    printf("Es ist dunkel in der Höhle. Du siehst eine Truhe. Willst du sie öffnen? (ja/nein)\n");
    scanf("%9s", choice);
    if (strcmp(choice, "ja") == 0) {
        printf("Die Truhe enthält Gold! Glückwunsch, du hast gewonnen.\n");
    } else {
        printf("Du verlässt die Höhle ohne die Truhe zu öffnen. Ende.\n");
    }
}