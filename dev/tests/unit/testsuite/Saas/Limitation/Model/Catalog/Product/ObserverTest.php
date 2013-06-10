<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_limitation;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var Saas_Limitation_Model_Catalog_Product_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_limitation = $this->getMock(
            'Saas_Limitation_Model_Catalog_Product_Limitation', array(), array(), '', false
        );
        $this->_session = $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false);
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Observer($this->_limitation, $this->_session);
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Creation restricted
     */
    public function testRestrictEntityCreationRestricted()
    {
        $entity = $this->getMock('Mage_Catalog_Model_Product', array(), array(), '', false);
        $entity->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('product' => $entity))));
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $this->_limitation->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue('Creation restricted'));
        $this->_model->restrictEntityCreation($observer);
    }

    public function testRestrictEntityCreationNonRestricted()
    {
        $entity = $this->getMock('Mage_Catalog_Model_Product', array(), array(), '', false);
        $entity->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('product' => $entity))));
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(false));
        $this->_model->restrictEntityCreation($observer);
    }

    public function testRestrictEntityCreationUpdate()
    {
        $entity = $this->getMock('Mage_Catalog_Model_Product', array(), array(), '', false);
        $entity->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(false));
        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('product' => $entity))));
        $this->_limitation->expects($this->never())
            ->method('isCreateRestricted');
        $this->_model->restrictEntityCreation($observer);
    }

    public function testDisplayNotificationRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $restrictionMessage = 'restriction message';
        $this->_limitation->expects($this->once())
            ->method('getCreateRestrictedMessage')
            ->will($this->returnValue($restrictionMessage));
        $this->_session->expects($this->once())
            ->method('addNotice')
            ->with($restrictionMessage);
        $this->_model->displayNotification(new Varien_Event_Observer);
    }

    public function testDisplayNotificationNonRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(false));
        $this->_limitation->expects($this->never())
            ->method('getCreateRestrictedMessage');
        $this->_session->expects($this->never())
            ->method('addNotice');
        $this->_model->displayNotification(new Varien_Event_Observer);
    }

    public function testDisableCreationButtonRestricted()
    {
        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue(true));
        $block = $this->getMock('Mage_Adminhtml_Block_Catalog_Product', array('updateButton'), array(), '', false);
        $block->expects($this->at(0))
            ->method('updateButton')
            ->with('add_new', 'disabled', true);
        $block->expects($this->at(1))
            ->method('updateButton')
            ->with('add_new', 'has_split', false);
        $observer = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->disableCreationButton($observer);
    }

    /**
     * @param string $blockClass
     * @param bool $isLimitationReached
     * @dataProvider disableCreationButtonNonRestrictedDataProvider
     */
    public function testDisableCreationButtonNonRestricted($blockClass, $isLimitationReached)
    {
        $this->_limitation->expects($this->any())
            ->method('isCreateRestricted')
            ->will($this->returnValue($isLimitationReached));
        $block = $this->getMock($blockClass, array('updateButton'), array(), '', false);
        $block->expects($this->never())
            ->method('updateButton');
        $observer = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->disableCreationButton($observer);
    }

    /**
     * @return array
     */
    public function disableCreationButtonNonRestrictedDataProvider()
    {
        return array(
            'product block, limitation is not reached'     => array('Mage_Adminhtml_Block_Catalog_Product', false),
            'non-product block, limitation is not reached' => array('Some_Block', false),
            'non-product block, limitation is reached'     => array('Some_Block', true),
        );
    }

    /**
     * @param bool|null $isProductNew
     * @param int $expectedProductsCount
     * @dataProvider removeRestrictedSavingButtonsDataProvider
     */
    public function testRemoveRestrictedSavingButtonsRestricted($isProductNew, $expectedProductsCount)
    {
        if (null === $isProductNew) {
            $product = null;
        } else {
            $product = $this->getMock('Mage_Catalog_Model_Product', array(), array(), '', false);
            $product->expects($this->once())
                ->method('isObjectNew')
                ->will($this->returnValue($isProductNew));
        }

        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->with($expectedProductsCount)
            ->will($this->returnValue(true));
        $block = $this->getMock('Mage_Adminhtml_Block_Catalog_Product_Edit', array(), array(), '', false);
        $block->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $splitBlock = $this->getMock(
            'Mage_Backend_Block_Widget_Button_Split', array('getOptions', 'setData'), array(), '', false
        );
        $block->expects($this->once())
            ->method('getChildBlock')
            ->with('save-split-button')
            ->will($this->returnValue($splitBlock));

        $origOptions = array(
            array('id' => 'button-1'),
            array('id' => 'new-button'),
            array('id' => 'duplicate-button'),
            array('id' => 'button-2'),
        );
        $expectedOptions = array(
            array('id' => 'button-1'),
            array('id' => 'button-2'),
        );
        $splitBlock->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($origOptions));
        $splitBlock->expects($this->once())
            ->method('setData')
            ->with('options', $expectedOptions);

        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->removeRestrictedSavingButtons($observer);
    }

    /**
     * @param bool|null $isProductNew
     * @param int $expectedProductsCount
     * @dataProvider removeRestrictedSavingButtonsDataProvider
     */
    public function testRemoveRestrictedSavingButtonsAllowed($isProductNew, $expectedProductsCount)
    {
        if (null === $isProductNew) {
            $product = null;
        } else {
            $product = $this->getMock('Mage_Catalog_Model_Product', array(), array(), '', false);
            $product->expects($this->once())
                ->method('isObjectNew')
                ->will($this->returnValue($isProductNew));
        }

        $this->_limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->with($expectedProductsCount)
            ->will($this->returnValue(false));
        $block = $this->getMock('Mage_Adminhtml_Block_Catalog_Product_Edit', array(), array(), '', false);
        $block->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $block->expects($this->never())
            ->method('getChildBlock');

        $observer  = new Varien_Event_Observer(array('event' => new Varien_Object(array('block' => $block))));
        $this->_model->removeRestrictedSavingButtons($observer);
    }

    /**
     * @return array
     */
    public function removeRestrictedSavingButtonsDataProvider()
    {
        return array(
            'no product' => array(null, 2),
            'new product' => array(true, 2),
            'existing product' => array(false, 1),
        );
    }
}
