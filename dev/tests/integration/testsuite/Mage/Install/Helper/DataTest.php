<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testCleanVarFolder()
    {
        $rootFolder = Mage::getConfig()->getVarDir() . DIRECTORY_SEPARATOR . 'parentFolder';
        $subFolderA = $rootFolder . DIRECTORY_SEPARATOR . 'subFolderA' . DIRECTORY_SEPARATOR;
        $subFolderB = $rootFolder . DIRECTORY_SEPARATOR . 'subFolderB' . DIRECTORY_SEPARATOR;
        @mkdir($subFolderA, 0777, true);
        @mkdir($subFolderB, 0777, true);
        @file_put_contents($subFolderB . 'test.txt', 'Some text here');
        $helper = new Mage_Install_Helper_Data();
        $helper->setVarSubFolders(array($rootFolder));
        $helper->cleanVarFolder();
        $this->assertFalse(is_dir($rootFolder));
    }
}
