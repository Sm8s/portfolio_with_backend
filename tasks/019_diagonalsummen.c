
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
    printf("Summe Hauptdiagonale: %d
", sum_main);
    printf("Summe Nebendiagonale: %d
", sum_sec);
    return 0;
}
