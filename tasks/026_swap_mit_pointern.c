
/*
 * Task 26: Swap mit Pointern
 * Beschreibung: Dieses Programm demonstriert das Tauschen zweier Werte mittels
 * Zeigern.
 */
#include <stdio.h>

static void swap(int *a, int *b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

int main(void) {
    int x, y;
    printf("Zahl 1: ");
    scanf("%d", &x);
    printf("Zahl 2: ");
    scanf("%d", &y);
    printf("Vor dem Tausch: x=%d y=%d
", x, y);
    swap(&x, &y);
    printf("Nach dem Tausch: x=%d y=%d
", x, y);
    return 0;
}
