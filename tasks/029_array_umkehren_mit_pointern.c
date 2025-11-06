
/*
 * Task 29: Array umkehren mit Pointern
 * Beschreibung: Dieses Programm kehrt ein Array in-place um, indem zwei
 * Zeiger verwendet werden, die sich aufeinander zubewegen.
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Laenge des Arrays: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 200) return 0;
    int a[200];
    for (int i = 0; i < n; i++) {
        printf("a[%d] = ", i);
        scanf("%d", &a[i]);
    }
    int *left = &a[0];
    int *right = &a[n - 1];
    while (left < right) {
        int tmp = *left;
        *left = *right;
        *right = tmp;
        left++;
        right--;
    }
    printf("Umgekehrtes Array: ");
    for (int i = 0; i < n; i++) printf("%d ", a[i]);
    printf("
");
    return 0;
}
