
/*
 * Task 32: Rechteck vs. Kreis
 * Beschreibung: Dieses Programm vergleicht die Flaechen von Rechteck und Kreis
 * anhand benutzerdefinierter Eingaben.
 */
#include <stdio.h>
#include <math.h>

typedef struct {
    double breite;
    double hoehe;
} Rechteck;

typedef struct {
    double radius;
} Kreis;

static double area_rect(const Rechteck *r) {
    return r->breite * r->hoehe;
}

static double area_circle(const Kreis *c) {
    return M_PI * c->radius * c->radius;
}

int main(void) {
    Rechteck r;
    Kreis c;
    printf("Rechteck Breite und Hoehe: ");
    scanf("%lf %lf", &r.breite, &r.hoehe);
    printf("Kreis Radius: ");
    scanf("%lf", &c.radius);
    double area_r = area_rect(&r);
    double area_c = area_circle(&c);
    printf("Flaeche Rechteck: %.2f
", area_r);
    printf("Flaeche Kreis: %.2f
", area_c);
    if (area_r > area_c) printf("Das Rechteck hat die groessere Flaeche.
");
    else if (area_c > area_r) printf("Der Kreis hat die groessere Flaeche.
");
    else printf("Beide Figuren haben die gleiche Flaeche.
");
    return 0;
}
