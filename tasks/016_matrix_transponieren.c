
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
    printf("Transponierte Matrix:
");
    for (int j = 0; j < m; j++) {
        for (int i = 0; i < n; i++) {
            printf("%d ", a[i][j]);
        }
        printf("
");
    }
    return 0;
}
