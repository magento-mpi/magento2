<?php
// Output report on theme layouts
$rootDir = realpath(__DIR__ . '/../../..');

$themeFiles = new GlobIterator($rootDir . '/app/design/frontend/enterprise/fixed/*/layout/*.xml');
$overriddenFiles = new GlobIterator($rootDir . '/app/design/frontend/enterprise/fixed/*/layout/override/*.xml');

$layoutFiles = new AppendIterator();
$layoutFiles->append($themeFiles);
$layoutFiles->append($overriddenFiles);

$totalFiles = 0;
foreach ($layoutFiles as $file) {
    $file = str_replace('\\', '/', $file);
    echo $file, "\n";
    $xml = simplexml_load_file_without_header($file);
    $strXml = $xml->asXML();
    echo 'Total lines: ' , substr_count($strXml, "\n"), "\n";
    echo 'Total bytes: ' , strlen($strXml), "\n";
    $totalFiles++;
}
echo "\n";
echo "Total files: {$totalFiles}\n";

function simplexml_load_file_without_header($file)
{
    $contents = file_get_contents($file);
    $cleanedContents = preg_replace('#\?>.*<layout#sU', '?><layout', $contents);
    if ($cleanedContents == $contents) {
        throw new Exception("Couldn't replace licence header in file {$file}");
    }
    return new SimpleXMLElement($cleanedContents);
}
