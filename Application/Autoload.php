<?php

namespace Application;

use \RecursiveIteratorIterator, \RecursiveDirectoryIterator, \UnexpectedValueException;

function D (string $A, ?string $B = APPLICATION)
{
    try {
        // Create an iterator to recursively scan the directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($B, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        // Loop through each file and directory
        foreach ($iterator as $fileInfo) {
            // Check if the current item is a file and its name matches
            if ($fileInfo->isFile() && $fileInfo->getFilename() === $A) {
                // Return the full, absolute path to the file
                return $fileInfo->getRealPath();
            }
        }
    } catch (UnexpectedValueException $e) {
        // This can happen if a directory is not readable.
        // You can log the error here if needed: error_log($e->getMessage());
        return null;
    }

    // Return null if the loop completes and the file is not found
    return null;
}

spl_autoload_register (function ($A) {
    $B = A ([$A, "php"], EXTENSION_SEPARATOR);
    $C = D ($B);
    require_once $C;
});
