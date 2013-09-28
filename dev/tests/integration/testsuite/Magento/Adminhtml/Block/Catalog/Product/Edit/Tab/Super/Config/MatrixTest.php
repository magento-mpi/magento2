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

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config;

/**
 * @magentoAppArea adminhtml
 */
class MatrixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testGetVariations()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_product', \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product')->load(1));
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Core\Block\Text', 'head');
        /** @var $usedAttribute \Magento\Catalog\Model\Entity\Attribute */
        $usedAttribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Model\Entity\Attribute')->loadByCode(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config')->
                    getEntityType('catalog_product')->getId(),
                'test_configurable'
            );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config_Matrix */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock(preg_replace('/Test$/', '', __CLASS__));

        $variations = $block->getVariations();
        foreach ($variations as &$variation) {
            foreach ($variation as &$row) {
                unset($row['price']);
            }
        }

        $this->assertEquals(
            array(
                array($usedAttribute->getId() => $attributeOptions[0]),
                array($usedAttribute->getId() => $attributeOptions[1]),
            ),
            $variations
        );
    }
}
