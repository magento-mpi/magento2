<?php
/**
 * Automated replacement of license notice into placeholders
 *
 * {license_notice}
 *
 * @category Magento
 * @package tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

$options = getopt('', array('source::'));
$baseDir = realpath(__DIR__ . '/../../..');
if (isset($options['source'])) {
    $sourceDir = realpath($options['source']);
    if (strpos($sourceDir, $baseDir) !== 0) {
        die('Incorrect source dir specified');
    }
} else {
    $sourceDir = $baseDir;
}

// scan for files (split up into several calls to overcome maximum limitation of 260 chars in glob pattern)
$files = globDir($sourceDir, '*.{xml*,xsd,mxml}', GLOB_BRACE);
$files = array_merge($files, globDir($sourceDir, '*.{php,php.sample,*htm*,css,js,as,sql}', GLOB_BRACE));

// exclude files from blacklist
$blacklist = require __DIR__ . '/blacklist.php';
foreach ($blacklist as $item) {
    $excludeDirs = glob("{$sourceDir}/{$item}", GLOB_ONLYDIR) ?: array();
    foreach ($excludeDirs as $excludeDir) {
        foreach ($files as $i => $file) {
            if (0 === strpos($file, $excludeDir)) {
                unset($files[$i]);
            }
        }
    }
    if (!$excludeDirs) {
        $excludeFiles = glob("{$sourceDir}/{$item}", GLOB_BRACE) ?: array();
        foreach ($excludeFiles as $excludeFile) {
            $i = array_search($excludeFile, $files);
            if (false !== $i) {
                unset($files[$i]);
            }
        }
    }
}

// replace
$licensePlaceholder = ' * {license_notice}' . "\n";
$replacements = array(
    array('/\s\*\sMagento.+?NOTICE OF LICENSE.+?DISCLAIMER.+?@/s', $licensePlaceholder . " *\n * @"),
);
foreach ($files as $file) {
    $content = file_get_contents($file);
    $newContent = $content;
    foreach ($replacements as $row) {
        list($regex, $replacement) = $row;
        $newContent = preg_replace($regex, $replacement, $content);
        if ($newContent != $content) {
            break;
        }
    }
    $newContent = preg_replace('/^(\s\*\s@copyright\s*).+?$/m', '\\1{copyright}', $newContent);
    $newContent = preg_replace('/^(\s\*\s@license\s*).+$/m', '\\1{license_link}', $newContent);
    $newContent = preg_replace('/(\{license\}.+?)\n\n\ \*/s', '\\1' . " *", $newContent);
    if ($newContent != $content) {
        echo $file . "\n";
        file_put_contents($file, $newContent);
    }
}

/**
 * Perform a glob search in specified directory
 *
 * @param string $dir
 * @param string $filesPattern
 * @param int $flags
 * @return array
 */
function globDir($dir, $filesPattern, $flags)
{
    if (!$dir || !is_dir($dir)) {
        return array();
    }
    $result = glob($dir . '/' . $filesPattern, $flags) ?: array();
    $dirs = glob($dir . '/*', GLOB_ONLYDIR) ?: array();
    foreach ($dirs as $innerDir) {
        $result = array_merge($result, globDir($innerDir, $filesPattern, $flags));
    }
    return $result;
}
