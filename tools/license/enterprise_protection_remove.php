<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Tools
 * @package    License
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

require dirname(__FILE__) . '/config.php';

// Void main
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

if (php_sapi_name() != 'cli') {
    echo "<pre>";
}

$scanningFolder = realpath(BP) . '/app/code/core/Enterprise/Enterprise';

$enterpriseFolder = realpath(BP) . '/app/code/core/Enterprise';

echo "Scanning project for license dummy files...\n\n";

$filesToScan = `cd $scanningFolder && find . -name '*.php'`;
$match = "~ * Ancestor class\n.*abstract class ([^\ ]*) extends ([^\ ]*)\n\{\n\}~is";
foreach (explode("\n", $filesToScan) as $file) {
    $currentFile = realpath($scanningFolder) . ltrim($file, '.');

    if (preg_match($match, file_get_contents($currentFile), $matches)) {
        $currentClassName = $matches[1];
        $currentParentName = $matches[2];
        echo "Processing '".$currentClassName."' dummy class...\n";

        $classChilds = explode("\n", trim(`grep -R 'extends $currentClassName' $enterpriseFolder | grep -v svn | cut -d ':' -f 1`));

        echo "\tFound ". count($classChilds) . " children\n";
        foreach ($classChilds as $child) {
            echo "\tChanging $child\n";flush();
            `sed -i 's/$currentClassName/$currentParentName/g' $child`;
        }
        echo "Removing $currentFile\n";
        `rm -rf $currentFile`;
        flush();
        echo "\n";
    }
}

echo "Done.\n";
if (php_sapi_name() != 'cli') {
    echo "</pre>";
}