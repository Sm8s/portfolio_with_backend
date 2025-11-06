
/*
 * Task 22: Groesster gemeinsamer Teiler
 * Beschreibung: Berechnet den groessten gemeinsamen Teiler zweier Zahlen
 * mittels rekursivem Euklidischen Algorithmus.
 */
#include <stdio.h>

static int ggt(int a, int b) {
    return (b == 0) ? (a >= 0 ? a : -a) : ggt(b, a % b);
}

int main(void) {
    int a, b;
    printf("Zwei Zahlen (a b): ");
    if (scanf("%d %d", &a, &b) != 2) return 0;
    printf("GGT(%d, %d) = %d
", a, b, ggt(a, b));
    return 0;
}
