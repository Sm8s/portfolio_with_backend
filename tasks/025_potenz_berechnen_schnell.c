
/*
 * Task 25: Potenz berechnen (schnell)
 * Beschreibung: Berechnet x^n effizient mit Schnell-Exponentiation.
 */
#include <stdio.h>

static long long fast_pow(long long x, long long n) {
    if (n == 0) return 1;
    if (n % 2 == 0) {
        long long half = fast_pow(x, n / 2);
        return half * half;
    }
    return x * fast_pow(x, n - 1);
}

int main(void) {
    long long x, n;
    printf("Basis x und Exponent n: ");
    if (scanf("%lld %lld", &x, &n) != 2) return 0;
    long long result = fast_pow(x, n);
    printf("%lld^%lld = %lld
", x, n, result);
    return 0;
}
