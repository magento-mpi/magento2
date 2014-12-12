#!/usr/bin/php
<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
require_once realpath(__DIR__ . '/../../../app/autoload.php');

define(
    'USAGE',
<<<USAGE
php -f bump-copyright-year.php -- -w <dir> [-y <year>]
    -w <dir>       use specified working dir
    [-y <year>]    use specified year instead of current
USAGE
);

try {
    $shortOpts = 'w:y:';
    $options = getopt($shortOpts);
    assertCondition(isset($options['w']), USAGE);
    $workingDir = $options['w'];
    $year = isset($options['y']) ? (int)$options['y'] : date('Y');
    $newCopyright = '@copyright Copyright (c) ' . $year . ' X.commerce, Inc.';
    processDir($workingDir);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}

/**
 * Process given file
 *
 * @param string $path
 * @return void
 */
function processFile($path)
{
    global $newCopyright;
    if (strpos($path, '/vendor/') !== false) {
        return;
    }

    if (preg_match('/\.(jpe?g|png|gif|ttf|swf|eot|woff|pdf|mp3|pdf|jar|jbf)$/', $path)) {
        return;
    }

    if (!is_readable($path)) {
        return;
    }

    file_put_contents(
        $path,
        preg_replace(
            '/@copyright Copyright \(c\) \d+ X\.commerce, Inc\./',
            $newCopyright,
            file_get_contents($path)
        )
    );
}

/**
 * Process given directory recursively
 *
 * @param string $path
 * @return void
 */
function processDir($path)
{
    static $excluded = ['.', '..', '.git', '.idea'];

    if (is_dir($path)) {
        $handle = opendir($path);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                $fullPath = rtrim($path, '/') . '/' . $file;
                if (is_file($fullPath)) {
                    processFile($fullPath);
                } elseif (!in_array($file, $excluded)) {
                    processDir($fullPath);
                }
            }
            closedir($handle);
        }
    }
}

/**
 * A basic assertion
 *
 * @param bool $condition
 * @param string $error
 * @return void
 * @throws \Exception
 */
function assertCondition($condition, $error)
{
    if (!$condition) {
        throw new \Exception($error);
    }
}
