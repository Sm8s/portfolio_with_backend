
/*
 * Task 23: Tuerme von Hanoi
 * Beschreibung: Dieses Programm loest das Tuerme-von-Hanoi-Problem rekursiv
 * und gibt die benoetigten Schritte aus.
 */
#include <stdio.h>

static void hanoi(int n, char from, char to, char aux) {
    if (n == 0) return;
    hanoi(n - 1, from, aux, to);
    printf("Bewege Scheibe %d von %c nach %c
", n, from, to);
    hanoi(n - 1, aux, to, from);
}

int main(void) {
    int n;
    printf("Anzahl der Scheiben: ");
    if (scanf("%d", &n) != 1 || n < 0) return 0;
    hanoi(n, 'A', 'C', 'B');
    return 0;
}
