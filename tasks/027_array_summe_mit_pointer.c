
/*
 * Task 27: Array-Summe mit Pointer
 * Beschreibung: Dieses Programm berechnet die Summe der Elemente eines Arrays
 * ausschliesslich mit Pointer-Arithmetik.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int sum = 0;
    int *ptr = a;
    for (int i = 0; i < n; i++) {
        sum += *(ptr++);
    }
    printf("Summe = %d
", sum);
    return 0;
}
