<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_Html_FooterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    protected function setUp()
    {
        Mage::getDesign()->setDefaultDesignTheme();
        $this->_theme = Mage::getDesign()->getDesignTheme();
    }

    public function testGetCacheKeyInfo()
    {
        $block = Mage::app()->getLayout()->createBlock('Magento_Page_Block_Html_Footer');
        $storeId = Mage::app()->getStore()->getId();
        $this->assertEquals(
            array('PAGE_FOOTER', $storeId, 0, $this->_theme->getId(), null),
            $block->getCacheKeyInfo()
        );
    }
}
