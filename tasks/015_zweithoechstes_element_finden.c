
/*
 * Task 15: Zweithoechstes Element finden
 * Beschreibung: Dieses Programm findet das zweithöchste Element in einem Array,
 * ohne das Array zu sortieren. Negative Zahlen werden unterstützt.
 */
#include <stdio.h>
#include <limits.h>

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n < 2 || n > 200) {
        printf("Mindestens zwei Elemente sind erforderlich.
");
        return 0;
    }
    int max1 = INT_MIN, max2 = INT_MIN;
    for (int i = 0; i < n; i++) {
        int x;
        printf("a[%d] = ", i);
        scanf("%d", &x);
        if (x > max1) {
            max2 = max1;
            max1 = x;
        } else if (x > max2 && x < max1) {
            max2 = x;
        }
    }
    if (max2 == INT_MIN) {
        printf("Kein eindeutiges Zweitmaximum gefunden (alle Werte gleich?).
");
    } else {
        printf("Zweithoechstes Element: %d (Max: %d)
", max2, max1);
    }
    return 0;
}
