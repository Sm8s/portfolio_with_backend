
/*
 * Task 30: Dynamisches Array
 * Beschreibung: Dieses Programm demonstriert die Verwendung von malloc(),
 * um ein Array dynamisch zu erzeugen, Elemente einzulesen und am Ende
 * den Speicher wieder freizugeben.
 */
#include <stdio.h>
#include <stdlib.h>

int main(void) {
    int n;
    printf("Anzahl der Elemente: ");
    if (scanf("%d", &n) != 1 || n <= 0) return 0;
    int *arr = malloc((size_t)n * sizeof(int));
    if (!arr) {
        printf("Speicherreservierung fehlgeschlagen.
");
        return 0;
    }
    for (int i = 0; i < n; i++) {
        printf("arr[%d] = ", i);
        scanf("%d", &arr[i]);
    }
    printf("Eingegebene Elemente: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", arr[i]);
    }
    printf("
");
    free(arr);
    return 0;
}
