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
 * Test class for Magento_Catalog_Model_Layer_Filter_Item.
 */
class Magento_Catalog_Model_Layer_Filter_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Layer_Filter_Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Layer_Filter_Item', array(
            'data' => array(
                'filter' => Mage::getModel('Magento_Catalog_Model_Layer_Filter_Category'),
                'value'  => array('valuePart1', 'valuePart2'),
            )
        ));
    }

    public function testGetFilter()
    {
        $filter = $this->_model->getFilter();
        $this->assertInternalType('object', $filter);
        $this->assertSame($filter, $this->_model->getFilter());
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testGetFilterException()
    {
        /** @var $model Magento_Catalog_Model_Layer_Filter_Item */
        $model = Mage::getModel('Magento_Catalog_Model_Layer_Filter_Item');
        $model->getFilter();
    }

    public function testGetUrl()
    {
        $action = Mage::getModel(
            'Magento_Core_Controller_Front_Action',
            array(
                'request' => new Magento_TestFramework_Request(),
                'response' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->get('Magento_TestFramework_Response'),
            )
        );
        Mage::app()->getFrontController()->setAction($action); // done in action's constructor
        $this->assertStringEndsWith('/?cat%5B0%5D=valuePart1&cat%5B1%5D=valuePart2', $this->_model->getUrl());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetRemoveUrl()
    {
        Mage::app()->getRequest()->setRoutingInfo(array(
            'requested_route'      => 'x',
            'requested_controller' => 'y',
            'requested_action'     => 'z',
        ));

        $request = new Magento_TestFramework_Request();
        $request->setParam('cat', 4);
        $this->_model->getFilter()->apply($request, Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text'));

        $this->assertStringEndsWith('/x/y/z/?cat=3', $this->_model->getRemoveUrl());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    public function testGetValueString()
    {
        $this->assertEquals('valuePart1,valuePart2', $this->_model->getValueString());
    }
}
