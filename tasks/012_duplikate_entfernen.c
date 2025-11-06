
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
        printf("Ungueltige Eingabe.
");
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
    printf("
");
    return 0;
}
