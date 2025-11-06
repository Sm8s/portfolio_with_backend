
/*
 * Task 34: Bibliothekssystem
 * Beschreibung: Dieses Programm implementiert ein einfaches Bibliothekssystem
 * zur Verwaltung von Buechern. Es erlaubt das Hinzufuegen von Buechern, die
 * Anzeige aller Buecher, das Ausleihen und Zurueckgeben sowie die Suche
 * nach Titeln. Zum Zwecke der Übersicht wird eine feste Obergrenze von
 * 100 Buechern gesetzt.
 */
#include <stdio.h>
#include <string.h>

typedef struct {
    char titel[100];
    char autor[100];
    char isbn[20];
    int entliehen; // 0 = verfuegbar, 1 = entliehen
} Buch;

static void add_book(Buch *buecher, int *count) {
    if (*count >= 100) {
        printf("Bibliothek ist voll!
");
        return;
    }
    Buch *b = &buecher[*count];
    printf("Titel: ");
    scanf("%99s", b->titel);
    printf("Autor: ");
    scanf("%99s", b->autor);
    printf("ISBN: ");
    scanf("%19s", b->isbn);
    b->entliehen = 0;
    (*count)++;
}

static void list_books(const Buch *buecher, int count) {
    for (int i = 0; i < count; i++) {
        printf("%d: %s von %s (ISBN: %s) – %s
", i, buecher[i].titel, buecher[i].autor,
               buecher[i].isbn, buecher[i].entliehen ? "entliehen" : "verfuegbar");
    }
}

static void lend_book(Buch *buecher, int count) {
    int idx;
    printf("Index des Buches ausleihen: ");
    scanf("%d", &idx);
    if (idx < 0 || idx >= count) {
        printf("Ungueltiger Index!
");
        return;
    }
    if (buecher[idx].entliehen) {
        printf("Buch ist bereits entliehen.
");
    } else {
        buecher[idx].entliehen = 1;
        printf("Buch ausgeliehen.
");
    }
}

static void return_book(Buch *buecher, int count) {
    int idx;
    printf("Index des Buches zurueckgeben: ");
    scanf("%d", &idx);
    if (idx < 0 || idx >= count) {
        printf("Ungueltiger Index!
");
        return;
    }
    if (!buecher[idx].entliehen) {
        printf("Buch ist nicht entliehen.
");
    } else {
        buecher[idx].entliehen = 0;
        printf("Buch zurueckgegeben.
");
    }
}

static void search_title(const Buch *buecher, int count) {
    char query[100];
    printf("Titel suchen: ");
    scanf("%99s", query);
    for (int i = 0; i < count; i++) {
        if (strstr(buecher[i].titel, query)) {
            printf("Gefunden: %d: %s von %s (ISBN: %s) – %s
", i, buecher[i].titel, buecher[i].autor,
                   buecher[i].isbn, buecher[i].entliehen ? "entliehen" : "verfuegbar");
        }
    }
}

int main(void) {
    Buch buecher[100];
    int count = 0;
    int choice;
    while (1) {
        printf("1: Hinzufuegen | 2: Anzeigen | 3: Ausleihen | 4: Zurueckgeben | 5: Suchen | 0: Ende → ");
        if (scanf("%d", &choice) != 1) break;
        if (choice == 0) break;
        if (choice == 1) add_book(buecher, &count);
        else if (choice == 2) list_books(buecher, count);
        else if (choice == 3) lend_book(buecher, count);
        else if (choice == 4) return_book(buecher, count);
        else if (choice == 5) search_title(buecher, count);
    }
    return 0;
}
