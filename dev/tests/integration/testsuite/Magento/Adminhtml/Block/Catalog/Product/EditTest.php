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


namespace Magento\Adminhtml\Block\Catalog\Product;

/**
 * @magentoAppArea adminhtml
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Catalog\Product\Edit
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->getMock(
            'Magento\Catalog\Model\Product', array('getAttributes', '__wakeup'), array(), '', false
        );
        $product->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product);
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit');
    }

    public function testGetTypeSwitcherData()
    {
        $data = json_decode($this->_block->getTypeSwitcherData(), true);
        $this->assertEquals('simple', $data['current_type']);
        $this->assertEquals(array(), $data['attributes']);
    }
}
