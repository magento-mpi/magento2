<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/CatalogRule/_files/catalog_rule_10_off_not_logged.php
 * @magentoAppArea adminhtml
 */
class Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_CatalogruleTest extends PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        /** @var Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule $block */
        $block = Mage::app()->getLayout()->createBlock(
            'Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule'
        );

        /** @var Magento_CatalogRule_Model_Rule $catalogRule */
        $catalogRule = Mage::getModel('Magento_CatalogRule_Model_Rule');
        $catalogRule->load('Test Catalog Rule', 'name');

        $this->assertSame(array($catalogRule->getId()), $block->getCollection()->getAllIds());
    }
}
