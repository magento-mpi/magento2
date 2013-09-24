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
 * Test class for \Magento\Catalog\Model\Layer\Filter\Item.
 */
namespace Magento\Catalog\Model\Layer\Filter;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Item', array(
            'data' => array(
                'filter' => \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Category'),
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
     * @expectedException \Magento\Core\Exception
     */
    public function testGetFilterException()
    {
        /** @var $model \Magento\Catalog\Model\Layer\Filter\Item */
        $model = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Item');
        $model->getFilter();
    }

    public function testGetUrl()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $action = \Mage::getModel(
            'Magento\Core\Controller\Front\Action',
            array(
                'request' => $request,
                'response' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->get('Magento_TestFramework_Response'),
            )
        );
        \Mage::app()->getFrontController()->setAction($action); // done in action's constructor
        $this->assertStringEndsWith('/?cat%5B0%5D=valuePart1&cat%5B1%5D=valuePart2', $this->_model->getUrl());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetRemoveUrl()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');

        \Mage::app()->getRequest()->setRoutingInfo(array(
            'requested_route'      => 'x',
            'requested_controller' => 'y',
            'requested_action'     => 'z',
        ));

        $request->setParam('cat', 4);
        $this->_model->getFilter()->apply($request, \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text'));

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
