/*
 * Einfaches Pong-Spiel im Terminal. Dies ist eine vereinfachte Umsetzung
 * ohne externe Bibliotheken. Für eine grafische Version nutze Bibliotheken
 * wie SDL, SFML oder Raylib.
 */
#include <stdio.h>
#include <unistd.h>
#include <termios.h>
#include <fcntl.h>

#define WIDTH 40
#define HEIGHT 20

int ballX = WIDTH / 2;
int ballY = HEIGHT / 2;
int ballDX = 1;
int ballDY = 1;
int paddleY = HEIGHT / 2 - 2;

int kbhit(void) {
    struct termios oldt, newt;
    int ch;
    int oldf;

    tcgetattr(STDIN_FILENO, &oldt);
    newt = oldt;
    newt.c_lflag &= ~(ICANON | ECHO);
    tcsetattr(STDIN_FILENO, TCSANOW, &newt);
    oldf = fcntl(STDIN_FILENO, F_GETFL, 0);
    fcntl(STDIN_FILENO, F_SETFL, oldf | O_NONBLOCK);

    ch = getchar();

    tcsetattr(STDIN_FILENO, TCSANOW, &oldt);
    fcntl(STDIN_FILENO, F_SETFL, oldf);

    if (ch != EOF) {
        ungetc(ch, stdin);
        return 1;
    }

    return 0;
}

void draw() {
    system("clear");
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            if (x == 1 && y >= paddleY && y < paddleY + 4) {
                printf("|"); // Paddle
            } else if (x == ballX && y == ballY) {
                printf("O");
            } else {
                printf(" ");
            }
        }
        printf("\n");
    }
}

void update() {
    ballX += ballDX;
    ballY += ballDY;
    // Bounce off top/bottom
    if (ballY <= 0 || ballY >= HEIGHT - 1) ballDY *= -1;
    // Bounce off paddle or left wall
    if (ballX <= 2) {
        if (ballY >= paddleY && ballY < paddleY + 4) {
            ballDX *= -1;
        } else {
            // Reset ball
            ballX = WIDTH / 2;
            ballY = HEIGHT / 2;
        }
    }
    // Bounce off right wall
    if (ballX >= WIDTH - 1) ballDX *= -1;
}

int main(void) {
    while (1) {
        draw();
        if (kbhit()) {
            int ch = getchar();
            if (ch == 'q') break;
            if (ch == 'w' && paddleY > 0) paddleY--;
            if (ch == 's' && paddleY < HEIGHT - 4) paddleY++;
        }
        update();
        usleep(50000);
    }
    return 0;
}