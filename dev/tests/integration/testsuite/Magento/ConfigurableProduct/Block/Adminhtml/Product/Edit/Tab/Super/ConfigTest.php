<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super;

/**
 * @magentoAppArea adminhtml
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSelectedAttributesForSimpleProductType()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')
            ->register('current_product', $objectManager->create('Magento\Catalog\Model\Product'));
        /** @var $block \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config */
        $block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config');
        $this->assertEquals(array(), $block->getSelectedAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetSelectedAttributesForConfigurableProductType()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')
            ->register('current_product', $objectManager->create('Magento\Catalog\Model\Product')->load(1));
        $objectManager->get('Magento\View\LayoutInterface')->createBlock('Magento\View\Element\Text', 'head');
        $usedAttribute = $objectManager->get('Magento\Catalog\Model\Entity\Attribute')->loadByCode(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config')
                ->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        /** @var $block \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config */
        $block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config');
        $selectedAttributes = $block->getSelectedAttributes();
        $this->assertEquals(array($usedAttribute->getId()), array_keys($selectedAttributes));
        $selectedAttribute = reset($selectedAttributes);
        $this->assertEquals('test_configurable', $selectedAttribute->getAttributeCode());
    }
}
