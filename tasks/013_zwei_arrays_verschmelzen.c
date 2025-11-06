
/*
 * Task 13: Zwei Arrays verschmelzen
 * Beschreibung: Dieses Programm verschmilzt zwei sortierte Arrays zu
 * einem neuen sortierten Array.
 */
#include <stdio.h>

int main(void) {
    int n, m;
    printf("Laenge des ersten Arrays (n): ");
    if (scanf("%d", &n) != 1 || n < 0 || n > 100) return 0;
    printf("Laenge des zweiten Arrays (m): ");
    if (scanf("%d", &m) != 1 || m < 0 || m > 100) return 0;
    int A[100], B[100], C[205];
    printf("Erstes Array sortiert:
");
    for (int i = 0; i < n; i++) {
        printf("A[%d] = ", i);
        scanf("%d", &A[i]);
    }
    printf("Zweites Array sortiert:
");
    for (int j = 0; j < m; j++) {
        printf("B[%d] = ", j);
        scanf("%d", &B[j]);
    }
    int i = 0, j = 0, k = 0;
    while (i < n && j < m) {
        if (A[i] <= B[j]) {
            C[k++] = A[i++];
        } else {
            C[k++] = B[j++];
        }
    }
    while (i < n) C[k++] = A[i++];
    while (j < m) C[k++] = B[j++];
    printf("Verschmolzenes Array: ");
    for (int t = 0; t < k; t++) printf("%d ", C[t]);
    printf("
");
    return 0;
}
