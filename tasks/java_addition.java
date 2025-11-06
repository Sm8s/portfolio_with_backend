import java.util.Scanner;

public class Addition {
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        System.out.print("Erste Zahl: ");
        int a = scanner.nextInt();
        System.out.print("Zweite Zahl: ");
        int b = scanner.nextInt();
        System.out.println("Summe: " + (a + b));
        scanner.close();
    }
}