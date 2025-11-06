
/*
 * Task 35: Zeitrechner
 * Beschreibung: Dieses Programm definiert eine Struktur fuer Zeiten und
 * implementiert Funktionen zum Addieren und Subtrahieren zweier Zeiten.
 */
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    int h, m, s;
} Zeit;

static void print_time(const Zeit *t) {
    printf("%02d:%02d:%02d", t->h, t->m, t->s);
}

static Zeit add_times(Zeit a, Zeit b) {
    Zeit res;
    res.s = a.s + b.s;
    res.m = a.m + b.m + res.s / 60;
    res.h = a.h + b.h + res.m / 60;
    res.s %= 60;
    res.m %= 60;
    return res;
}

static Zeit diff_times(Zeit a, Zeit b) {
    // Berechne a - b (als Absolutwert)
    Zeit res;
    int sec_a = a.h * 3600 + a.m * 60 + a.s;
    int sec_b = b.h * 3600 + b.m * 60 + b.s;
    int diff = abs(sec_a - sec_b);
    res.h = diff / 3600;
    res.m = (diff % 3600) / 60;
    res.s = diff % 60;
    return res;
}

int main(void) {
    Zeit t1, t2;
    printf("Zeit 1 (h m s): ");
    scanf("%d %d %d", &t1.h, &t1.m, &t1.s);
    printf("Zeit 2 (h m s): ");
    scanf("%d %d %d", &t2.h, &t2.m, &t2.s);
    Zeit sum = add_times(t1, t2);
    Zeit diff = diff_times(t1, t2);
    printf("Summe: ");
    print_time(&sum);
    printf("
Differenz: ");
    print_time(&diff);
    printf("
");
    return 0;
}
