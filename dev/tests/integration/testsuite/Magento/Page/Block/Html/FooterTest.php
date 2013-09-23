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
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface');
        $this->_theme = $design->setDefaultDesignTheme()->getDesignTheme();
    }

    public function testGetCacheKeyInfo()
    {
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Page_Block_Html_Footer');
        $storeId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getId();
        $this->assertEquals(
            array('PAGE_FOOTER', $storeId, 0, $this->_theme->getId(), null),
            $block->getCacheKeyInfo()
        );
    }
}
