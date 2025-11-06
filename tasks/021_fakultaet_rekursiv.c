
/*
 * Task 21: Fakultaet rekursiv
 * Beschreibung: Berechnet die Fakultaet einer nichtnegativen Zahl rekursiv.
 */
#include <stdio.h>

static long long factorial(int n) {
    return (n <= 1) ? 1 : n * factorial(n - 1);
}

int main(void) {
    int n;
    printf("n? ");
    if (scanf("%d", &n) != 1 || n < 0) {
        printf("Ungueltige Eingabe.
");
        return 0;
    }
    printf("%d! = %lld
", n, factorial(n));
    return 0;
}
