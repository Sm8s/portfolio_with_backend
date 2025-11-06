
/*
 * Task 11: Bubble Sort
 * Beschreibung: Dieses Programm sortiert ein Array mittels Bubble Sort und
 * zeigt nach jedem Durchlauf den aktuellen Zustand des Arrays.
 */
#include <stdio.h>

static void print_array(const int *a, int n) {
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("
");
}

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 100) {
        printf("Ungueltige Eingabe.
");
        return 0;
    }
    int a[100];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    for (int pass = 0; pass < n - 1; pass++) {
        for (int i = 0; i < n - 1 - pass; i++) {
            if (a[i] > a[i + 1]) {
                int tmp = a[i];
                a[i] = a[i + 1];
                a[i + 1] = tmp;
            }
        }
        printf("Nach Durchlauf %d: ", pass + 1);
        print_array(a, n);
    }
    printf("Sortiertes Array: ");
    print_array(a, n);
    return 0;
}
