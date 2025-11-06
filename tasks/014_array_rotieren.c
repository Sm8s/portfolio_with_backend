
/*
 * Task 14: Array rotieren
 * Beschreibung: Dieses Programm rotiert ein Array um K Positionen nach rechts.
 */
#include <stdio.h>

// Hilfsfunktion, um ein Array in-place zu reversen
static void reverse(int *a, int start, int end) {
    while (start < end) {
        int tmp = a[start];
        a[start] = a[end];
        a[end] = tmp;
        start++;
        end--;
    }
}

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int k;
    printf("K (Anzahl der Positionen): ");
    if (scanf("%d", &k) != 1) return 0;
    k = ((k % n) + n) % n; // Normalisieren fuer negative Werte
    // Dreifach-Reversal: gesamtes Array, erstes Segment, zweites Segment
    reverse(a, 0, n - 1);
    reverse(a, 0, k - 1);
    reverse(a, k, n - 1);
    printf("Rotiertes Array: ");
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("
");
    return 0;
}
