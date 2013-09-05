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

/**
 * Test class for Magento_Catalog_Model_Layer_Filter_Attribute.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/_files/attribute_with_option.php
 */
class Magento_Catalog_Model_Layer_Filter_AttributeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Layer_Filter_Attribute
     */
    protected $_model;

    /**
     * @var int
     */
    protected $_attributeOptionId;

    protected function setUp()
    {
        /** @var $attribute Magento_Catalog_Model_Entity_Attribute */
        $attribute = Mage::getModel('Magento_Catalog_Model_Entity_Attribute');
        $attribute->loadByCode('catalog_product', 'attribute_with_option');
        foreach ($attribute->getSource()->getAllOptions() as $optionInfo) {
            if ($optionInfo['label'] == 'Option Label') {
                $this->_attributeOptionId = $optionInfo['value'];
                break;
            }
        }

        $this->_model = Mage::getModel('Magento_Catalog_Model_Layer_Filter_Attribute');
        $this->_model->setData(array(
            'layer' => Mage::getModel('Magento_Catalog_Model_Layer'),
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

        $request = new Magento_TestFramework_Request();
        $request->setParam('attribute', array());
        $this->_model->apply($request, Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text'));

        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testApply()
    {
        $this->assertEmpty($this->_model->getLayer()->getState()->getFilters());

        $request = new Magento_TestFramework_Request();
        $request->setParam('attribute', $this->_attributeOptionId);
        $this->_model->apply($request, Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text'));

        $this->assertNotEmpty($this->_model->getLayer()->getState()->getFilters());
    }

    public function testGetItems()
    {
        $items = $this->_model->getItems();

        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items));

        /** @var $item Magento_Catalog_Model_Layer_Filter_Item */
        $item = $items[0];

        $this->assertInstanceOf('Magento_Catalog_Model_Layer_Filter_Item', $item);
        $this->assertSame($this->_model, $item->getFilter());
        $this->assertEquals('Option Label', $item->getLabel());
        $this->assertEquals($this->_attributeOptionId, $item->getValue());
        $this->assertEquals(1, $item->getCount());
    }
}
