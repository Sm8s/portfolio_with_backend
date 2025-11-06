/*
 * Mini-Rogue-Like Beispiel:
 * Dies ist ein stark vereinfachtes Beispiel für ein Dungeon-Spiel. Es soll
 * zeigen, wie man ein Spielfeld generiert und den Spieler bewegen kann. Für
 * ein vollständiges Spiel müssen Sie Monster, Items, Kämpfe und Levelaufstieg
 * implementieren.
 */

#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#define WIDTH  20
#define HEIGHT 10

char map[HEIGHT][WIDTH+1];
int playerX, playerY;

void generate_map() {
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            if (rand() % 10 < 2) {
                map[y][x] = '#'; // Wand
            } else {
                map[y][x] = '.'; // Boden
            }
        }
        map[y][WIDTH] = '\0';
    }
    // Anfangsposition des Spielers
    playerX = WIDTH / 2;
    playerY = HEIGHT / 2;
    map[playerY][playerX] = '@';
}

void draw_map() {
    for (int y = 0; y < HEIGHT; y++) {
        printf("%s\n", map[y]);
    }
}

void move_player(int dx, int dy) {
    int newX = playerX + dx;
    int newY = playerY + dy;
    if (newX < 0 || newX >= WIDTH || newY < 0 || newY >= HEIGHT) {
        return;
    }
    if (map[newY][newX] == '#') {
        return;
    }
    map[playerY][playerX] = '.';
    playerX = newX;
    playerY = newY;
    map[playerY][playerX] = '@';
}

int main(void) {
    srand((unsigned)time(NULL));
    generate_map();
    char input;
    printf("Bewege dich mit WASD durch das Dungeon. 'q' zum Beenden.\n");
    while (1) {
        draw_map();
        printf("Eingabe: ");
        scanf(" %c", &input);
        if (input == 'q') break;
        switch (input) {
            case 'w': move_player(0, -1); break;
            case 's': move_player(0, 1); break;
            case 'a': move_player(-1, 0); break;
            case 'd': move_player(1, 0); break;
        }
    }
    return 0;
}