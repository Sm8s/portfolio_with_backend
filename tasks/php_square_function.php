<?php
function square($n) {
    return $n * $n;
}

$n = (int) readline("Zahl eingeben: ");
echo "Das Quadrat von $n ist " . square($n) . "\n";
?>