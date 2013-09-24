<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions;

/**
 * @magentoDataFixture Magento/CatalogRule/_files/catalog_rule_10_off_not_logged.php
 * @magentoAppArea adminhtml
 */
class CatalogruleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        /** @var \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Catalogrule $block */
        $block = \Mage::app()->getLayout()->createBlock(
            'Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Catalogrule'
        );

        /** @var \Magento\CatalogRule\Model\Rule $catalogRule */
        $catalogRule = \Mage::getModel('Magento\CatalogRule\Model\Rule');
        $catalogRule->load('Test Catalog Rule', 'name');

        $this->assertSame(array($catalogRule->getId()), $block->getCollection()->getAllIds());
    }
}
