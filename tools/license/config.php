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
 * @category    Tools
 * @package     License
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

// tested under Windows only

define('NOTICE_OSL',
'/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    {category}
 * @package     {package}
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */');

define('NOTICE_AFL',
'/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    {category}
 * @package     {package}
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */');

define('NOTICE_EE',
'/**
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
 * @category    {category}
 * @package     {package}
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */');

define('PHOENIX_OSL',
'/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    {category}
 * @package     {package}
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */');

define('BP', realpath(dirname(__FILE__) . '/../..'));

/**
 * Adds to a PHP or PHTML-file with or without notice of license
 * File should start from <?php
 */
define('REGEX_PHP', '/^\<\?php\s+(\/\*\*.+NOTICE OF LICENSE.+?\*\/\s?)?(.*)$/us');
define('REPLACEMENT_PHP', "<?php\n{notice}\n\\2");

/**
 * Adds to a JS or CSS-file with or without notice of license
 * File may start from whitespace
 * Known issue: can break javascript files with non-Magento license
 */
define('REGEX_SKIN', '/^\s*(\/\*\*.+NOTICE OF LICENSE.+?\*\/\s?)?(.+)$/us');
define('REPLACEMENT_SKIN', "{notice}\n\\2");

/**
 * Replaces an existing notice of license in an XML-file
 * Not supposed to work without notice of license
 */
define('REGEX_XML', '/^\<\?xml version\=\"1\.0\"\?\>\s+\<\!\-\-\s+(\/\*\*.+NOTICE OF LICENSE.+?\*\/\s?)?(.+)$/us');
define('REPLACEMENT_XML', "<?xml version=\"1.0\"?" . ">\n<!--\n{notice}\n\\2");

/**
 * Dive into directories recursively and gather all files by masks
 *
 * @param string|array $paths
 * @param string|array $fileMasks
 * @param array &$result
 */
function globRecursive($paths, $fileMasks, &$result, $isRecursion = false)
{
    static $skipDirectories = null;
    static $skipFiles = null;

    if (!empty($paths)) {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        if (!$isRecursion) {
            $skipDirectories = array();
            $skipFiles = array();
            foreach ($paths as $k => $path) {
                if (false !== strpos($path, '!')) {
                    $real = realpath(str_replace('!', '', $path));
                    if (is_dir($real)) {
                        $skipDirectories[] = $real;
                    } elseif (is_file($real)) {
                        $skipFiles[] = $real;
                    }
                    unset($paths[$k]);
                }
            }
        }
        foreach ($paths as $dir) {
            $skip = false;
            foreach ($skipDirectories as $skipDir) {
                if (false !== strpos(realpath($dir), $skipDir)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            globRecursive(glob($dir . '/*', GLOB_ONLYDIR), $fileMasks, $result, true);
            if (!is_array($fileMasks)) {
                $fileMasks = array($fileMasks);
            }
            foreach ($fileMasks as $filesMask) {
                foreach (glob($dir . '/' . $filesMask) as $filename) {
                    if (!in_array(realpath($filename), $skipFiles)) {
                        $result[] = $filename;
                    }
                }
            }
        }
    }
}

/**
 * Update license notice in specified directories and file paths
 *
 * Optionally can:
 *  - convert trailing CRLF into LF (will also trim trailing spaces),
 *  - replace tab indents to spaces
 *  - add LF to the end of file if not exists
 *
 * @param string|array $directories - relative to magento root
 * @param string|array $fileMasks   - glob()-compatible
 * @param string $regex             - PCRE
 * @param string $replacement       - with {notice} variable
 * @param string $noticeOfLicense   - license notice text with {category} and {package}
 * @param string|array $categoryPackageCallback - either array or name of function that gets filename and returns array($category, $package)
 * @param bool $crlfToLf
 * @param bool $tabsToSpaces
 * @param bool $LfBeforeEof
 * @return int - cumulative count of changed files
 */
function updateLicense($directories, $fileMasks, $regex, $replacement, $noticeOfLicense, $categoryPackageCallback, $crlfToLf = false, $tabsToSpaces = false, $LfBeforeEof = false)
{
    static $changedFilesCounter = 0;
    if (!is_array($directories)) {
        $directories = BP . '/' . trim($directories, '/');
    }
    else {
        foreach ($directories as $key => $dir) {
            $directories[$key] = BP . '/' . trim($dir, '/');
        }
    }

    $foundFiles = array();
    globRecursive($directories, $fileMasks, $foundFiles);

    foreach ($foundFiles as $filename) {
        $contents = file_get_contents($filename);
        $newContents = $contents;
        // trim lines and/or replace tabs to spaces
        if (($crlfToLf || $tabsToSpaces) && (false !== strpos($newContents, "\r\n") || false !== strpos($newContents, "\t"))) {
            $newContents = '';
            foreach (file($filename) as $line) {
                if ($tabsToSpaces) {
                    if (preg_match('/^(\s*' . "\t" . '+\s*?)/', $line, $matches)) {
                        $replace = str_replace("\t", '    ', $matches[1]);
                        $line = preg_replace('/^(' . preg_quote($matches[1], '/') . ')/', $replace, $line);
                    }
                }
                if ($crlfToLf) {
                    $line = rtrim($line);
                }
                $newContents .= $line . "\n";
            }
        }
        elseif ($LfBeforeEof && !preg_match('/' . "\n" . '$/s', $newContents)) {
            $newContents = rtrim($newContents) . "\n";
        }
        list($category, $package) = (is_array($categoryPackageCallback) ? $categoryPackageCallback : $categoryPackageCallback($filename));
        $readyNotice = str_replace(array('{category}', '{package}'), array($category, $package), $noticeOfLicense);
        $newContents = preg_replace($regex, str_replace('{notice}', $readyNotice, $replacement), $newContents);
        if ($contents !== $newContents) {
            file_put_contents($filename, $newContents);
            $changedFilesCounter++;
        }
    }
    return $changedFilesCounter;
}

/**
 * Get rid of code pool part of path and treat next two as category & package
 *
 * @param unknown_type $filename
 * @return unknown
 */
function coreCodePoolCallback($filename)
{
    list($category, $package) = explode('/', str_replace(BP . '/app/code/core/', '', $filename));
    return array($category, "{$category}_{$package}");
}

/**
 * Get category & package from filename of module definition xml-file
 *
 * @param string $filename
 * @return array
 */
function xmlModulesCallback($filename)
{
    $package = str_replace('.xml', '', basename($filename));
    list($category) = explode('_', $package);
    if ('Mage_All' === $package) {
        $package = 'Mage_Core';
    }
    return array($category, $package);
}

/**
 * Get category & package name from filename of a theme file
 *
 * @param string $filename
 * @return array
 */
function themeCallback($filename)
{
    list(, $package) = explode('app/design/', $filename);
    list(, $packagePart1, $packagePart2) = explode('/', $package);
    return array('design', "{$packagePart1}_{$packagePart2}");
}

/**
 * Get category & package name from filename of a skin file
 *
 * @param string $filename
 * @return array
 */
function skinCallback($filename)
{
    list(, $package) = explode('skin/', $filename);
    list(, $packagePart1, $packagePart2) = explode('/', $package);
    return array('design', "{$packagePart1}_{$packagePart2}");
}
