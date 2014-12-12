<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Catalogrule'
        );

        /** @var \Magento\CatalogRule\Model\Rule $catalogRule */
        $catalogRule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogRule\Model\Rule'
        );
        $catalogRule->load('Test Catalog Rule', 'name');

        $this->assertSame([$catalogRule->getId()], $block->getCollection()->getAllIds());
    }
}
