/*
 * Programm: Quadrat‑Funktion
 * Beschreibung: Definiert eine Funktion, die das Quadrat einer Zahl berechnet,
 *               und demonstriert ihre Verwendung.
 */
#include <stdio.h>

int quadrat(int x) {
    return x * x;
}

int main(void) {
    int n;
    printf("Zahl eingeben: ");
    if (scanf("%d", &n) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 1;
    }
    printf("Das Quadrat von %d ist %d.\n", n, quadrat(n));
    return 0;
}