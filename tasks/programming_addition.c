/*
 * Programm: Einfache Addition
 * Beschreibung: Liest zwei ganze Zahlen von der Eingabe ein und gibt ihre Summe aus.
 */
#include <stdio.h>

int main(void) {
    int a, b;
    printf("Erste Zahl: ");
    if (scanf("%d", &a) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 1;
    }
    printf("Zweite Zahl: ");
    if (scanf("%d", &b) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 1;
    }
    printf("Summe: %d\n", a + b);
    return 0;
}