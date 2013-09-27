<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSelectedAttributesForSimpleProductType()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_product', $objectManager->create('Magento\Catalog\Model\Product'));
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config */
        $block = $objectManager->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config');
        $this->assertEquals(array(), $block->getSelectedAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testGetSelectedAttributesForConfigurableProductType()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_product', $objectManager->create('Magento\Catalog\Model\Product')->load(1));
        $objectManager->get('Magento\Core\Model\Layout')->createBlock('Magento\Core\Block\Text', 'head');
        $usedAttribute = $objectManager->get('Magento\Catalog\Model\Entity\Attribute')->loadByCode(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config')
                ->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config */
        $block = $objectManager->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config');
        $selectedAttributes = $block->getSelectedAttributes();
        $this->assertEquals(array($usedAttribute->getId()), array_keys($selectedAttributes));
        $selectedAttribute = reset($selectedAttributes);
        $this->assertEquals('test_configurable', $selectedAttribute->getAttributeCode());
    }
}
