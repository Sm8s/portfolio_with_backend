
/*
 * Task 24: Binaere Suche rekursiv
 * Beschreibung: Dieses Programm demonstriert die rekursive binäre Suche in
 * einem sortierten Array. Das Array muss vorab sortiert sein.
 */
#include <stdio.h>

static int binary_search(const int *a, int left, int right, int target) {
    if (left > right) return -1;
    int mid = left + (right - left) / 2;
    if (a[mid] == target) return mid;
    if (a[mid] > target) return binary_search(a, left, mid - 1, target);
    return binary_search(a, mid + 1, right, target);
}

int main(void) {
    int n;
    printf("Laenge des sortierten Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 100) return 0;
    int a[100];
    printf("Geben Sie %d sortierte Werte ein:
", n);
    for (int i = 0; i < n; i++) {
        scanf("%d", &a[i]);
    }
    int target;
    printf("Zielwert: ");
    scanf("%d", &target);
    int pos = binary_search(a, 0, n - 1, target);
    if (pos >= 0) {
        printf("Wert %d gefunden an Position %d.
", target, pos);
    } else {
        printf("Wert %d nicht gefunden.
", target);
    }
    return 0;
}
