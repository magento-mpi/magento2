<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Attribute.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/_files/attribute_with_option.php
 */
class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Attribute
     */
    protected $_model;

    /**
     * @var int
     */
    protected $_attributeOptionId;

    protected function setUp()
    {
        /** @var $attribute \Magento\Catalog\Model\Entity\Attribute */
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Entity\Attribute');
        $attribute->loadByCode('catalog_product', 'attribute_with_option');
        foreach ($attribute->getSource()->getAllOptions() as $optionInfo) {
            if ($optionInfo['label'] == 'Option Label') {
                $this->_attributeOptionId = $optionInfo['value'];
                break;
            }
        }

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Attribute');
        $this->_model->setData(array(
            'layer' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer'),
            'attribute_model' => $attribute,
        ));
    }

    public function testOptionIdNotEmpty()
    {
        $this->assertNotEmpty($this->_attributeOptionId, 'Fixture attribute option id.'); // just in case
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('attribute', array());
        $this->_model->apply(
            $request,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Layout')
                ->createBlock('Magento\Core\Block\Text'))
        ;

        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testApply()
    {
        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('attribute', $this->_attributeOptionId);
        $this->_model->apply(
            $request,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Layout')
                ->createBlock('Magento\Core\Block\Text')
        );

        $this->assertNotEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testGetItems()
    {
        $items = $this->_model->getItems();

        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items));

        /** @var $item \Magento\Catalog\Model\Layer\Filter\Item */
        $item = $items[0];

        $this->assertInstanceOf('Magento\Catalog\Model\Layer\Filter\Item', $item);
        $this->assertSame($this->_model, $item->getFilter());
        $this->assertEquals('Option Label', $item->getLabel());
        $this->assertEquals($this->_attributeOptionId, $item->getValue());
        $this->assertEquals(1, $item->getCount());
    }
}
