
/*
 * Task 18: Spirale durch Matrix
 * Beschreibung: Dieses Programm gibt die Elemente einer quadratischen Matrix
 * in einer Spirale aus (im Uhrzeigersinn von außen nach innen).
 */
#include <stdio.h>

int main(void) {
    int n;
    printf("Groesse der quadratischen Matrix (n): ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 10) return 0;
    int a[10][10];
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n; j++) {
            printf("a[%d][%d] = ", i, j);
            scanf("%d", &a[i][j]);
        }
    }
    int top = 0, bottom = n - 1, left = 0, right = n - 1;
    printf("Spirale: ");
    while (top <= bottom && left <= right) {
        for (int j = left; j <= right; j++) printf("%d ", a[top][j]);
        top++;
        for (int i = top; i <= bottom; i++) printf("%d ", a[i][right]);
        right--;
        if (top <= bottom) {
            for (int j = right; j >= left; j--) printf("%d ", a[bottom][j]);
            bottom--;
        }
        if (left <= right) {
            for (int i = bottom; i >= top; i--) printf("%d ", a[i][left]);
            left++;
        }
    }
    printf("
");
    return 0;
}
