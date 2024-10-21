<?php
function shortestArray($input)
{
    $result = [];
    $maxValue = 8; // Maksymalna wartość w tablicy to 8

    while ($input > 0) {
        // Wybierz największą liczbę, która nie przekracza bieżącej wartości wejściowej
        $number = min($input, $maxValue);
        $result[] = $number; // Dodaj liczbę do wyniku
        $input -= $number; // Odejmij tę liczbę od wartości wejściowej
    }

    return $result;
}

// Przykłady użycia:
print_r(shortestArray(20)); // Wynik: [8, 8, 4]
print_r(shortestArray(7));  // Wynik: [7]
print_r(shortestArray(9));  // Wynik: [8, 1]
