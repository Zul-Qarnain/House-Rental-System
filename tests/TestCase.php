<?php
function assert_equal($expected, $actual, string $msg = ''): void {
    if ($expected !== $actual) {
        $expStr = var_export($expected, true);
        $actStr = var_export($actual, true);
        throw new Exception("FAIL: {$msg} — expected {$expStr}, got {$actStr}");
    }
}

function assert_true($condition, string $msg = ''): void {
    if (!$condition) {
        throw new Exception("FAIL: {$msg} — condition evaluated to false");
    }
}

function assert_throws(callable $fn, string $msg = ''): void {
    try {
        $fn();
    } catch (Throwable $e) {
        return; // Expected Exception caught
    }
    throw new Exception("FAIL: {$msg} — expected exception was not thrown");
}
