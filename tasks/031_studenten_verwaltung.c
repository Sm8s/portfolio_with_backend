
/*
 * Task 31: Studenten-Verwaltung
 * Beschreibung: Dieses Programm definiert eine Struktur fuer Studenten und
 * erlaubt die Verwaltung mehrerer Studenten, einschliesslich der Berechnung
 * der Durchschnittsnoten und dem Finden des besten Studenten.
 */
#include <stdio.h>
#include <string.h>

typedef struct {
    char name[50];
    int matrikel;
    float grades[10];
    int grade_count;
} Student;

static float average(const Student *s) {
    if (s->grade_count == 0) return 0.0f;
    float sum = 0.0f;
    for (int i = 0; i < s->grade_count; i++) sum += s->grades[i];
    return sum / s->grade_count;
}

int main(void) {
    int n;
    printf("Anzahl der Studenten: ");
    if (scanf("%d", &n) != 1 || n <= 0 || n > 50) return 0;
    Student students[50];
    for (int i = 0; i < n; i++) {
        printf("Student %d Name: ", i + 1);
        scanf("%s", students[i].name);
        printf("Matrikelnummer: ");
        scanf("%d", &students[i].matrikel);
        printf("Anzahl der Noten: ");
        scanf("%d", &students[i].grade_count);
        for (int j = 0; j < students[i].grade_count; j++) {
            printf("Note %d: ", j + 1);
            scanf("%f", &students[i].grades[j]);
        }
    }
    // Durchschnitt berechnen und besten Studenten finden
    int best_index = 0;
    float best_avg = average(&students[0]);
    for (int i = 1; i < n; i++) {
        float avg = average(&students[i]);
        if (avg > best_avg) {
            best_avg = avg;
            best_index = i;
        }
    }
    printf("Bester Student: %s (Matrikel %d) mit Durchschnitt %.2f
",
           students[best_index].name, students[best_index].matrikel, best_avg);
    return 0;
}
