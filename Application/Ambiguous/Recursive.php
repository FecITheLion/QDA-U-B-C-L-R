<?php

function recursive_existance(mixed $haystack, mixed $needle): bool
{
    if ($haystack === $needle) return true;
    if (is_array($haystack) || is_object($haystack)) {
        foreach ($haystack as $item) {
            if (recursive_existance($item, $needle)) {
                return true;
            }
        }
    }
    return false;
}
