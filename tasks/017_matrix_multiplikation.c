
/*
 * Task 17: Matrix-Multiplikation
 * Beschreibung: Dieses Programm multipliziert zwei Matrizen A (m×n) und
 * B (n×p) zu einer Ergebnis-Matrix C (m×p). Es wird überprüft, ob die
 * Multiplikation möglich ist.
 */
#include <stdio.h>

int main(void) {
    int m, n, p;
    printf("Dimensionen der Matrix A (m n): ");
    if (scanf("%d %d", &m, &n) != 2 || m <= 0 || n <= 0 || m > 5 || n > 5) return 0;
    printf("Dimensionen der Matrix B (n p): ");
    int n2;
    if (scanf("%d %d", &n2, &p) != 2 || p <= 0 || n2 != n || p > 5) return 0;
    int A[5][5], B[5][5], C[5][5];
    // Matrix A einlesen
    printf("Matrix A:
");
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < n; j++) {
            printf("A[%d][%d] = ", i, j);
            scanf("%d", &A[i][j]);
        }
    }
    // Matrix B einlesen
    printf("Matrix B:
");
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < p; j++) {
            printf("B[%d][%d] = ", i, j);
            scanf("%d", &B[i][j]);
        }
    }
    // Matrix C berechnen
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < p; j++) {
            int sum = 0;
            for (int k = 0; k < n; k++) {
                sum += A[i][k] * B[k][j];
            }
            C[i][j] = sum;
        }
    }
    printf("Produktmatrix C:
");
    for (int i = 0; i < m; i++) {
        for (int j = 0; j < p; j++) {
            printf("%d ", C[i][j]);
        }
        printf("
");
    }
    return 0;
}
