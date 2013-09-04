<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
// Intended to be run from the magento root

$files = `find . -name widget.xml`;
$xsl = 'dev/tools/xml/translate.xslt';
$saxon = 'dev/tools/xml/saxon9he.jar';

foreach (preg_split("/((\r?\n)|(\r\n?))/", $files) as $file) {
    if (!empty($file)) {
        $cmd = "java -jar $saxon -l:on -s:$file -xsl:$xsl -o:$file";
        echo "$cmd \n";
        echo shell_exec($cmd);
    }
}

