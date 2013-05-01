<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Catalog/_files/product_simple.php
 * @magentoDataFixture Mage/CatalogRule/_files/catalog_rule_10_off_not_logged.php
 * @magentoDataFixture Enterprise/Banner/_files/banner.php
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_CatalogruleTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        /** @var Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule $block */
        $block = Mage::app()->getLayout()->createBlock(
            'Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule'
        );

        /** @var Mage_CatalogRule_Model_Rule $catalogRule */
        $catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');
        $ruleId = $catalogRule->getCollection()->getFirstItem()->getId();

        $this->assertSame(array($ruleId), $block->getCollection()->getAllIds());
    }
}
