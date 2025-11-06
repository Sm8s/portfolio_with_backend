import java.util.Scanner;

public class SquareFunction {
    public static int square(int x) {
        return x * x;
    }

    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        System.out.print("Zahl eingeben: ");
        int n = scanner.nextInt();
        System.out.println("Das Quadrat von " + n + " ist " + square(n));
        scanner.close();
    }
}