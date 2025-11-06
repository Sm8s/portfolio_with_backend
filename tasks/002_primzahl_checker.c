
/*
 * Task 2: Primzahl-Checker
 * Beschreibung: Dieses Programm liest eine Zahl ein und überprüft,
 * ob sie eine Primzahl ist. Zur Optimierung wird nur bis zur Wurzel
 * der Zahl getestet.
 */
#include <stdio.h>
#include <math.h>

int main(void) {
    long long n;
    printf("Zahl? ");
    if (scanf("%lld", &n) != 1) {
        printf("Ungueltige Eingabe.
");
        return 0;
    }
    if (n < 2) {
        printf("%lld ist keine Primzahl.
", n);
        return 0;
    }
    if (n % 2 == 0) {
        printf("%lld ist %sPrimzahl.
", n, (n == 2) ? "eine " : "keine ");
        return 0;
    }
    int prim = 1;
    for (long long i = 3; i * i <= n; i += 2) {
        if (n % i == 0) {
            prim = 0;
            break;
        }
    }
    printf("%lld ist %sPrimzahl.
", n, prim ? "eine " : "keine ");
    return 0;
}
