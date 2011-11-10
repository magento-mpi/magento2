<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Cms
 */
class Mage_Cms_Block_Widget_BlockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Cms/_files/block.php
     * @magentoDataFixture Mage/Core/_files/variable.php
     * @magentoConfigFixture current_store web/unsecure/base_url http://example.com/
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://example.com/
     */
    public function testToHtml()
    {
        $cmsBlock = new Mage_Cms_Model_Block;
        $cmsBlock->load('fixture_block', 'identifier');
        $block = new Mage_Cms_Block_Widget_Block;
        $block->setBlockId($cmsBlock->getId());
        $block->toHtml();
        $result = $block->getText();
        $this->assertContains('<a href="http://example.com/', $result);
        $this->assertContains('<p>Config value: "http://example.com/".</p>', $result);
        $this->assertContains('<p>Custom variable: "HTML Value".</p>', $result);
    }
}
