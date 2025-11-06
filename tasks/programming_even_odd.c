/*
 * Programm: Gerade oder ungerade?
 * Beschreibung: Liest eine ganze Zahl und gibt aus, ob sie gerade oder ungerade ist.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Zahl eingeben: ");
    if (scanf("%d", &n) != 1) {
        printf("Ungueltige Eingabe.\n");
        return 1;
    }
    if (n % 2 == 0) {
        printf("Die Zahl ist gerade.\n");
    } else {
        printf("Die Zahl ist ungerade.\n");
    }
    return 0;
}