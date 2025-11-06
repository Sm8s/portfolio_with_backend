
/*
 * Task 33: Linked List
 * Beschreibung: Dieses Programm implementiert eine einfache singly linked list
 * mit Einfuegen am Ende, Loeschen nach Wert und Ausgeben der Liste.
 */
#include <stdio.h>
#include <stdlib.h>

typedef struct Node {
    int data;
    struct Node *next;
} Node;

static void print_list(const Node *head) {
    const Node *cur = head;
    while (cur) {
        printf("%d -> ", cur->data);
        cur = cur->next;
    }
    printf("NULL
");
}

static void push_back(Node **head, int value) {
    Node *new_node = malloc(sizeof(Node));
    new_node->data = value;
    new_node->next = NULL;
    if (!*head) {
        *head = new_node;
        return;
    }
    Node *cur = *head;
    while (cur->next) cur = cur->next;
    cur->next = new_node;
}

static void delete_value(Node **head, int value) {
    Node *cur = *head, *prev = NULL;
    while (cur) {
        if (cur->data == value) {
            if (prev) prev->next = cur->next;
            else *head = cur->next;
            free(cur);
            return;
        }
        prev = cur;
        cur = cur->next;
    }
}

int main(void) {
    Node *head = NULL;
    int choice;
    while (1) {
        printf("1: Einfuegen | 2: Loeschen | 3: Ausgeben | 0: Ende → ");
        if (scanf("%d", &choice) != 1) break;
        if (choice == 0) break;
        if (choice == 1) {
            int value;
            printf("Wert einfuegen: ");
            scanf("%d", &value);
            push_back(&head, value);
        } else if (choice == 2) {
            int value;
            printf("Wert loeschen: ");
            scanf("%d", &value);
            delete_value(&head, value);
        } else if (choice == 3) {
            print_list(head);
        }
    }
    // Liste freigeben
    Node *cur = head;
    while (cur) {
        Node *next = cur->next;
        free(cur);
        cur = next;
    }
    return 0;
}
