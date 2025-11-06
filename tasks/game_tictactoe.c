/*
 * Tic-Tac-Toe mit einfachem KI-Gegner. Das Spielfeld ist 3x3.
 */
#include <stdio.h>

char board[3][3];

void init_board() {
    for (int i = 0; i < 3; i++)
        for (int j = 0; j < 3; j++)
            board[i][j] = ' ';
}

void print_board() {
    printf("\n");
    for (int i = 0; i < 3; i++) {
        printf(" %c | %c | %c \n", board[i][0], board[i][1], board[i][2]);
        if (i < 2) printf("---+---+---\n");
    }
    printf("\n");
}

int check_win(char player) {
    for (int i = 0; i < 3; i++) {
        if (board[i][0] == player && board[i][1] == player && board[i][2] == player) return 1;
        if (board[0][i] == player && board[1][i] == player && board[2][i] == player) return 1;
    }
    if (board[0][0] == player && board[1][1] == player && board[2][2] == player) return 1;
    if (board[0][2] == player && board[1][1] == player && board[2][0] == player) return 1;
    return 0;
}

int is_full() {
    for (int i = 0; i < 3; i++)
        for (int j = 0; j < 3; j++)
            if (board[i][j] == ' ') return 0;
    return 1;
}

void player_move() {
    int row, col;
    do {
        printf("Dein Zug (Zeile Spalte): ");
        scanf("%d %d", &row, &col);
    } while (row < 1 || row > 3 || col < 1 || col > 3 || board[row-1][col-1] != ' ');
    board[row-1][col-1] = 'X';
}

// Einfache KI: Suche nach erstem freien Feld
void computer_move() {
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            if (board[i][j] == ' ') {
                board[i][j] = 'O';
                return;
            }
        }
    }
}

int main(void) {
    init_board();
    while (1) {
        print_board();
        player_move();
        if (check_win('X')) {
            print_board();
            printf("Du gewinnst!\n");
            break;
        }
        if (is_full()) {
            print_board();
            printf("Unentschieden!\n");
            break;
        }
        computer_move();
        if (check_win('O')) {
            print_board();
            printf("Computer gewinnt!\n");
            break;
        }
        if (is_full()) {
            print_board();
            printf("Unentschieden!\n");
            break;
        }
    }
    return 0;
}