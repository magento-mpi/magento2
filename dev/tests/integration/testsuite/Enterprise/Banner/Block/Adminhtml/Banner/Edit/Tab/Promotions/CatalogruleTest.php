<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/CatalogRule/_files/catalog_rule_10_off_not_logged.php
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_CatalogruleTest extends PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        /** @var Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule $block */
        $block = Mage::app()->getLayout()->createBlock(
            'Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule'
        );

        /** @var Mage_CatalogRule_Model_Rule $catalogRule */
        $catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');
        $catalogRule->load('Test Catalog Rule', 'name');

        $this->assertSame(array($catalogRule->getId()), $block->getCollection()->getAllIds());
    }
}
