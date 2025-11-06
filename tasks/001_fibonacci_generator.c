
/*
 * Task 1: Fibonacci-Generator
 * Beschreibung: Dieses Programm berechnet die ersten N Fibonacci-Zahlen
 * und gibt sie durch Komma getrennt aus. N wird vom Benutzer eingegeben.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("N? ");
    if (scanf("%d", &n) != 1 || n < 0) {
        printf("Ungueltige Eingabe.
");
        return 0;
    }
    long long a = 0, b = 1;
    for (int i = 0; i < n; i++) {
        printf("%lld%s", a, (i == n - 1) ? "" : ", ");
        long long next = a + b;
        a = b;
        b = next;
    }
    printf("
");
    return 0;
}
