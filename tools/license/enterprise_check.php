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

/**
 * Scan lines "class Foo extends Bar" in PHP source files
 *
 * @param string $directory
 * @param string $fileMask
 * @return array
 */
function scanClasses($directory, $fileMask='*.php')
{
    $foundFiles = $classParents = $sourceFiles = array();
    globRecursive($directory, $fileMask, $foundFiles);
    foreach ($foundFiles as $filename) {
        $tokens = token_get_all(file_get_contents($filename));
        $lastKeyword = $lastClass = NULL;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0]==T_STRING && !is_null($lastKeyword)) {
                    if ($lastKeyword==T_CLASS) {
                        $lastClass = $token[1];
                        $classParents[$token[1]] = 'StdClass';
                        $sourceFiles[$token[1]] = $filename;
                    } else {
                        $classParents[$lastClass] = $token[1];
                    }
                    $lastKeyword = NULL;
                } elseif (in_array($token[0], array(T_CLASS, T_EXTENDS))) {
                    $lastKeyword = $token[0];
                }
            }
        }
    }
    return array($classParents, $sourceFiles);
}

/**
 * Pack flat classes relations from scanClasses() to tree
 *
 * @param array $classParents
 * @return array
 */
function classesToTree($classParents)
{
    $treeRoot = $treeIndex = array();
    foreach ($classParents as $parent) {
        if (!isset($classParents[$parent])) {
            $treeRoot[$parent] = array();
            $treeIndex[$parent] =& $treeRoot[$parent];
        }
    }
    while (count($classParents)) {
        foreach ($classParents as $class=>$parent) {
            if (!isset($treeIndex[$parent])) {
                continue;
            }
            $treeIndex[$parent][$class] = array();
            $treeIndex[$class] =& $treeIndex[$parent][$class];
            unset($classParents[$class]);
            break;
        }
    }
    return $treeRoot;
}

/**
 * Check class tree for Enterprise_Enterprise protection layer
 *
 * @param array $sourceFiles
 * @param string $sourceDir
 * @param string $template
 * @param array $tree
 * @param string $parentClass
 */
function checkEnterpriseProtection($sourceFiles, $sourceDir, $template, $tree, $parentClass='StdClass')
{
    if( count($tree) ) {
        $isParentCommunity = preg_match('/^Mage_/', $parentClass);
        $isParentController = FALSE;
        $parentFile = NULL;
        if (isset($sourceFiles[$parentClass])) {
            $isParentController = preg_match('=/controllers/=', $sourceFiles[$parentClass]);
            if ($isParentController) {
                $parentFile = substr($sourceFiles[$parentClass], strlen($sourceDir));
            }
        }
        foreach ($tree as $class=>$subtree) {
            $isClassEnterprise = preg_match('/^Enterprise_/', $class);
            $isClassProtector = preg_match('/^Enterprise_Enterprise_/', $class);
            $isClassLicense = preg_match('/^Enterprise_License_/', $class);
            if ($isClassEnterprise && $isParentCommunity && !$isClassProtector && !$isClassLicense) {
                echo "Correction: $class extends $parentClass\n";
                $protectorClass = getProtectorName($parentClass, $isParentController);
                $protectorFile = getClassFile($protectorClass, $isParentController);
                $protectorRequireFile = NULL;
                if ($isParentController) {
                    $protectorRequireFile = substr($protectorFile, strlen($sourceDir));
                }
                modifyParentClass($sourceFiles[$class], $parentClass, $protectorClass, $protectorRequireFile);
                createProtectorLayer($template, $protectorFile, $protectorClass, $parentClass, $parentFile);
            }
            checkEnterpriseProtection($sourceFiles, $sourceDir, $template, $subtree, $class);
        }
    }
}

/**
 * Update Enterprise_Enterprise protection layer
 *
 * @param array $sourceFiles
 * @param string $sourceDir
 * @param string $template
 * @param array $tree
 * @param string $parentClass
 */
function updateEnterpriseProtection($sourceFiles, $sourceDir, $template, $tree, $parentClass='StdClass')
{
    if( count($tree) ) {
        $isParentCommunity = preg_match('/^Mage_/', $parentClass);
        $isParentController = FALSE;
        $parentFile = NULL;
        if (isset($sourceFiles[$parentClass])) {
            $isParentController = preg_match('=/controllers/=', $sourceFiles[$parentClass]);
            if ($isParentController) {
                $parentFile = substr($sourceFiles[$parentClass], strlen($sourceDir));
            }
        }
        foreach ($tree as $class=>$subtree) {
            $isClassEnterprise = preg_match('/^Enterprise_/', $class);
            $isClassProtector = preg_match('/^Enterprise_Enterprise_/', $class);
            $isClassLicense = preg_match('/^Enterprise_License_/', $class);
            if ($isClassProtector) {
                if (!isset($sourceFiles[$class])) {
                    die("Error: class $class undefined!\n");
                }
                $filename = $sourceFiles[$class];
                $oldFile = file_get_contents($filename);
                createProtectorLayer($template, $filename, $class, $parentClass, $parentFile); // overwrite
                $newFile = file_get_contents($filename);
                if ($oldFile!==$newFile) {
                    echo "Updating $class\n";
                }
            }
            updateEnterpriseProtection($sourceFiles, $sourceDir, $template, $subtree, $class);
        }
    }
}

/**
 * Convert Mage_X_Y_Foobar to Enterprise_Enterprise_Y_X_Foobar
 *
 * @param string $communityClass
 * @return string
 */
function getProtectorName($communityClass, $isController=FALSE)
{
    $tmp = explode('_', $communityClass);
    if ($isController) {
        unset($tmp[0]);
    } else {
        $tmp[0] = $tmp[2];
        unset($tmp[2]);
    }
    return 'Enterprise_Enterprise_'.join('_', $tmp);
}

/**
 * Get file name for class
 *
 * @param string $className
 * @param bool $isController
 * @return string
 */
function getClassFile($class, $isController=FALSE)
{
    if ($isController) {
        $regex = '/^(Mage_|Enterprise_Enterprise_)/';
        $replace = '$1controllers_';
        $class = preg_replace($regex, $replace, $class);
    }
    $name = strtr($class, '_', '/');
    return BP.'/app/code/core/'.$name.'.php';
}

/**
 * Rename parent class in PHP source
 *
 * @param string $fileToModify
 * @param string $oldParent
 * @param string $newParent
 */
function modifyParentClass($fileToModify, $oldParent, $newParent, $requireFile=NULL)
{
    $codeToModify = file_get_contents($fileToModify);
    $regex = '/extends\s+'.$oldParent.'/';
    $replace = 'extends '.$newParent;
    $codeToModify = preg_replace($regex, $replace, $codeToModify);
    if (!is_null($requireFile)) {
        $regex = '/((include|require)(_once)?\s)+[\'"]Mage\/.+?[\^\'"]+/';
        $replace = "$1 '$requireFile'";
        $codeToModify = preg_replace($regex, $replace, $codeToModify);
    }
    file_put_contents($fileToModify, $codeToModify);
}

/**
 * Create Enterprise_Enterprise_Foobar file
 *
 * @param string $protectorCode
 * @param string $protectorFile
 * @param string $protectorClass
 * @param string $parentClass
 */
function createProtectorLayer($protectorCode, $protectorFile, $protectorClass, $parentClass, $parentFile=NULL)
{
    if ($protectorClass=='Enterprise_Enterprise_Model_Observer' ||
        $protectorClass=='Enterprise_Enterprise_Model_Observer_Install') {
        // special case, do not touch these files
        return;
    }
    $dir = dirname($protectorFile);
    if (!is_dir($dir)) {
        mkdir(dirname($protectorFile), 0755, TRUE);
    }
    $protectorCode = str_replace('__ProtectorClass__', $protectorClass, $protectorCode);
    $protectorCode = str_replace('__ParentClass__', $parentClass, $protectorCode);
    if (is_null($parentFile)) {
        $protectorCode = preg_replace('/^.+__require__.+$/m', '', $protectorCode);
    } else {
        $protectorCode = str_replace('// __require__', "require_once '$parentFile';", $protectorCode);
    }
    file_put_contents($protectorFile, $protectorCode);
}

// Void main
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

if (isset($_SERVER['REQUEST_METHOD'])) {
    echo '<pre>';
}

$template = file_get_contents(dirname(__FILE__).'/enterprise_template.php');
if (isset($_SERVER['argv'][1])) {
    if (($_SERVER['argv'][1]=='clean')) {
        $template = file_get_contents(dirname(__FILE__).'/enterprise_template_clean.php');
    }
}
$template = str_replace("\r", '', $template);

list($classes, $sourceFiles) = scanClasses(BP.'/app/code/core');
$tree = classesToTree($classes);
checkEnterpriseProtection($sourceFiles, BP.'/app/code/core/', $template, $tree);
updateEnterpriseProtection($sourceFiles, BP.'/app/code/core/', $template, $tree);
echo "Done.\n";

//var_dump(getProtectorName('Mage_Customer_AccountController'));

//modifyParentClass(
//    '/home/gray/work/app/code/core/Enterprise/SalesArchive/controllers/Adminhtml/Sales/OrderController.php',
//    'Mage_Adminhtml_Sales_OrderController',
//    'Enterprise_Enterprise_Adminhtml_Sales_OrderController');
