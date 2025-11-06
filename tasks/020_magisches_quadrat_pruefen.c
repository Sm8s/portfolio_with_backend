
/*
 * Task 20: Magisches Quadrat pruefen
 * Beschreibung: Dieses Programm prüft, ob eine 3×3 Matrix ein magisches
 * Quadrat ist, d.h. ob die Summen aller Zeilen, Spalten und beider
 * Diagonalen gleich sind.
 */
#include <stdio.h>

int main(void) {
    int a[3][3];
    printf("Geben Sie die 3x3 Matrix ein:
");
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
    printf("Die Matrix ist %s ein magisches Quadrat.
", ok ? "" : "kein");
    return 0;
}
