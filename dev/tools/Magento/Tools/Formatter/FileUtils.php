<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/**
 * This method returns an array of non-commented out lines
 *
 * @param string $fileName Name of the file to read in
 * @return string[]
 */
function getLines($fileName)
{
    // return an array of non-commented out lines
    $returns = [];
    if (file_exists($fileName)) {
        // read in the lines from the file
        $lines = file($fileName);
        // go through each line and trim and discard the commented out lines
        foreach ($lines as $line) {
            $line = trim($line);
            if ('#' !== $line[0]) {
                array_push($returns, $line);
            }
        }
    } else {
        echo 'File not found: ' . $fileName . PHP_EOL;
    }
    return $returns;
}

/**
 * This method returns true if $haystack start with the string in $needle.
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function startsWith($haystack, $needle)
{
    return $needle === '' || strpos($haystack, $needle) === 0;
}

/**
 * This method returns the string with the directory separators normalized to /
 *
 * @param string $subject
 * @return string
 */
function normalizeDirectorySeparators($subject)
{
    return str_replace('\\', '/', $subject);
}

/**
 * This method returns the result of joining the directory to the filename.
 *
 * @param string $path
 * @param string $file
 * @return string
 */
function joinPaths($path, $file)
{
    return join('/', [rtrim($path, '/'), ltrim($file, '/')]);
}
