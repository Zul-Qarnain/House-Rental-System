<?php
// Custom Lightweight Test Runner (No PHPUnit / Composer)

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../core/' . $class . '.php',
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/TestCase.php';

$testFiles = glob(__DIR__ . '/*Test.php');
$passed = 0;
$failed = 0;

echo "========================================\n";
echo " Running Custom PropTech Suite Tests...\n";
echo "========================================\n\n";

foreach ($testFiles as $file) {
    require_once $file;
}

$userFunctions = get_defined_functions()['user'];
$testFunctions = array_filter($userFunctions, fn($f) => str_starts_with($f, 'test_'));

foreach ($testFunctions as $func) {
    echo "Running {$func}... ";
    try {
        $func();
        echo "\033[32mPASSED\033[0m\n";
        $passed++;
    } catch (Throwable $e) {
        echo "\033[31mFAILED\033[0m\n";
        echo "  --> " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n----------------------------------------\n";
echo "Summary: {$passed} Passed, {$failed} Failed.\n";
echo "----------------------------------------\n";

if ($failed > 0) {
    exit(1);
} else {
    exit(0);
}
