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
 */namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions;

class CatalogruleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        /** @var \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Catalogrule $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Catalogrule');

        /** @var \Magento\CatalogRule\Model\Rule $catalogRule */
        $catalogRule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogRule\Model\Rule');
        $catalogRule->load('Test Catalog Rule', 'name');

        $this->assertSame(array($catalogRule->getId()), $block->getCollection()->getAllIds());
    }
}
