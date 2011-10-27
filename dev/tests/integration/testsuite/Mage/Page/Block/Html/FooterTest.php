<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Page
 */
class Mage_Page_Block_Html_FooterTest extends PHPUnit_Framework_TestCase
{
    public function testGetCacheKeyInfo()
    {
        $block = new Mage_Page_Block_Html_Footer;
        $storeId = Mage::app()->getStore()->getId();
        $this->assertEquals(array('PAGE_FOOTER', $storeId, 0, 'default', 'default', null), $block->getCacheKeyInfo());
    }
}
