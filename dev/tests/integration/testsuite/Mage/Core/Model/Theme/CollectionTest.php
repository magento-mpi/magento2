<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for filesystem themes collection
 */
class Mage_Core_Model_Theme_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test load themes collection from filesystem
     *
     * @magentoAppIsolation enabled
     */
    public function testLoadThemesFromFileSystem()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(dirname(__DIR__));

        $baseDesignDir = implode(DS, array(__DIR__, '..', '_files', 'design'));
        $pathPattern = implode(DS, array('frontend', 'default', '*', 'theme.xml'));

        /** @var $collection Mage_Core_Model_Theme_Collection */
        $collection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $collection->setBaseDir($baseDesignDir)->addTargetPattern($pathPattern);

        $this->assertEquals(2, count($collection));
    }
}
