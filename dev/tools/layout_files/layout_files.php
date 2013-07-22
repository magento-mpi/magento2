<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scan enterprise/fixed theme and delete all the overridden layout files that just duplicate module files
 */
$rootDir = realpath(__DIR__ . '/../../..');

$enterpriseIterator = new GlobIterator($rootDir . '/app/design/frontend/enterprise/fixed/*/layout/override/*.xml');
$totalFiles = count($enterpriseIterator);
$totalBytes = 0;
$totalLines = 0;
$totalDeleted = $totalDeletedBytes = $totalDeletedLines = 0;
foreach ($enterpriseIterator as $file) {
    $xml = simplexml_load_file_without_header($file);
    $strXml = $xml->asXML();

    $parentFile = getParentFile($file, $rootDir);
    $parentXml = simplexml_load_file_without_header($parentFile);
    $parentStrXml = $parentXml->asXML();

    $totalLines += substr_count($strXml, "\n");
    $totalBytes += strlen($strXml);
    if ($strXml == $parentStrXml) {
        $totalDeleted++;
        $totalDeletedLines += substr_count($strXml, "\n");
        $totalDeletedBytes += strlen($strXml);
        unlink($file);
        echo "Deleted {$file}\n";
    }
}

echo "\nDeleted {$totalDeleted}/{$totalFiles} files ({$totalDeletedBytes}/{$totalBytes} bytes, {$totalDeletedLines}/{$totalLines} lines)";

//---------------------------------------------
/**
 * Remove license header, so it doesn't influence statistics
 *
 * @param string $file
 * @return SimpleXMLElement
 */
function simplexml_load_file_without_header($file)
{
    $contents = file_get_contents($file);
    $contents = preg_replace('#\?>.*<layout#sU', '?><layout', $contents);
    return new SimpleXMLElement($contents);
}

function getParentFile($file, $rootDir)
{
    if (!preg_match('#/frontend/enterprise/fixed/([^/]+)/#', $file, $matches)) {
        throw new Exception("Couldn't extract module name from {$file}");
    }

    list($namespace, $module) = explode('_', $matches[1]);
    $filename = basename($file);
    $parentFile = $rootDir . "/app/code/{$namespace}/{$module}/view/frontend/layout/$filename";
    if (!file_exists($parentFile)) {
        throw new Exception("Couldn't find parent file {$parentFile} for {$file}");
    }
    return $parentFile;
}
