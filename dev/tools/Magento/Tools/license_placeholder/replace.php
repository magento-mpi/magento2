<?php
/**
 * Automated replacement of license notice into placeholders
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

$sourceDir = realpath(__DIR__ . '/../../../../..');

// scan for files (split up into several calls to overcome maximum limitation of 260 chars in glob pattern)
$files = globDir($sourceDir, '*.{xml,xml.template,xml.additional,xml.dist,xml.sample,xsd,mxml,xsl}', GLOB_BRACE);
$files = array_merge($files, globDir($sourceDir, '*.{php,php.sample,phtml,html,htm,css,js,as,sql}', GLOB_BRACE));

// exclude files from blacklist
$blacklist = require __DIR__ . '/blacklist.php';
foreach ($blacklist as $item) {
    $excludeDirs = glob("{$sourceDir}/{$item}", GLOB_ONLYDIR) ?: [];
    foreach ($excludeDirs as $excludeDir) {
        foreach ($files as $i => $file) {
            if (0 === strpos($file, $excludeDir)) {
                unset($files[$i]);
            }
        }
    }
    if (!$excludeDirs) {
        $excludeFiles = glob("{$sourceDir}/{$item}", GLOB_BRACE) ?: [];
        foreach ($excludeFiles as $excludeFile) {
            $i = array_search($excludeFile, $files);
            if (false !== $i) {
                unset($files[$i]);
            }
        }
    }
}

// replace
$licensePlaceholder = ' * {license}' . "\n";
$replacements = [
    ['/\s\*\sMagento.+?NOTICE OF LICENSE.+?DISCLAIMER.+?@/s', $licensePlaceholder . " *\n * @"],
    ['/\ \*\ \{license_notice\}\s/s', $licensePlaceholder],
];
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
    $newContent = preg_replace('/^\s\*\s@copyright.+?$/m', '', $newContent);
    $newContent = preg_replace('/^\s\*\s@license.+$/m', '', $newContent);
    $newContent = preg_replace('/(\{license\}.+?)\n\n\ \*/s', '\\1' . " *", $newContent);
    if ($newContent != $content) {
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
        return [];
    }
    $result = glob($dir . '/' . $filesPattern, $flags) ?: [];
    $dirs = glob($dir . '/*', GLOB_ONLYDIR) ?: [];
    foreach ($dirs as $innerDir) {
        $result = array_merge($result, globDir($innerDir, $filesPattern, $flags));
    }
    return $result;
}
