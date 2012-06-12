<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_Item_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item_Validator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * Data to be validated
     *
     * @var array
     */
    protected $_params = array(
        'id' => 'item',
        'title' => 'Item Title',
        'action' => '/system/config',
        'resource' => 'system/config',
        'tooltip' => 'Item tooltip',
    );

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
        $this->_factoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_Backend_Helper_Data');
        $this->_urlModelMock = $this->getMock("Mage_Backend_Model_Url", array(), array(), '', false);

        $this->_params['acl'] = $this->_aclMock;
        $this->_params['objectFactory'] = $this->_factoryMock;
        $this->_params['module'] = $this->_helperMock;
        $this->_params['urlModel'] = $this->_urlModelMock;
        $this->_model = new Mage_Backend_Model_Menu_Item_Validator();
    }

    /**
     * @param string $requiredParam
     * @throws BadMethodCallException
     * @expectedException BadMethodCallException
     * @dataProvider requiredParamsProvider
     */
    public function testValidateWithMissingRequiredParamThrowsException($requiredParam)
    {
        try {
            unset($this->_params[$requiredParam]);
            $this->_model->validate($this->_params);
        } catch (BadMethodCallException $e) {
            $this->assertContains($requiredParam, $e->getMessage());
            throw $e;
        }
    }

    public function requiredParamsProvider()
    {
        return array(
            array('acl'),
            array('objectFactory'),
            array('urlModel'),
            array('id'),
            array('title'),
            array('module')
        );
    }

    /**
     * @param string $typedParam
     * @throws InvalidArgumentException
     * @expectedException InvalidArgumentException
     * @dataProvider requiredParamsProvider
     */
    public function testValidateWithWrongTypesThrowsException($typedParam)
    {
        try{
            $this->_params[$typedParam] = new Varien_Object();
            $this->_model->validate($this->_params);
        } catch (InvalidArgumentException $e) {
            $this->assertContains($typedParam, $e->getMessage());
            throw $e;
        }
    }

    public function typedParamsProvider()
    {
        return array(
            array('acl'),
            array('objectFactory'),
            array('urlModel'),
            array('moduleHelper')
        );
    }

    /**
     * @param string $param
     * @param mixed $invalidValue
     * @throws InvalidArgumentException
     * @expectedException InvalidArgumentException
     * @dataProvider invalidParamsProvider
     */
    public function testValidateWithNonValidPrimitivesThrowsException($param, $invalidValue)
    {
        try {
            $this->_params[$param] = $invalidValue;
            $this->_model->validate($this->_params);
        } catch (InvalidArgumentException $e) {
            $this->assertContains($param, $e->getMessage());
            throw $e;
        }
    }

    public function invalidParamsProvider()
    {
        return array(
            array('id', 'ab'),
            array('id', 'abc$'),
            array('title', 'a'),
            array('title', '123456789012345678901234567890123456789012345678901'),
            array('action', '1a'),
            array('action', '12b|'),
            array('resource', '1a'),
            array('resource', '12b|'),
            array('toolTip', 'a'),
            array('toolTip', '123456789012345678901234567890123456789012345678901'),
        );
    }

    /**
     *  Validate duplicated ids
     *
     * @param $existedItems
     * @param $newItem
     * @dataProvider duplicateIdsProvider
     * @expectedException InvalidArgumentException
     */
    public function testValidateWithDuplicateIdsThrowsException($existedItems, $newItem)
    {
        foreach ($existedItems as $item) {
            $item = array_merge($item, $this->_params);
            $this->_model->validate($item);
        }

        $newItem = array_merge($newItem, $this->_params);
        $this->_model->validate($newItem);
    }

    /**
     * Provide items with duplicates ids
     *
     * @return array
     */
    public function duplicateIdsProvider()
    {
        return array(
            array(
                array(
                    array(
                        'id' => 'item1',
                        'title' => 'Item 1',
                        'action' => 'adminhtml/controller/item1'
                    ),
                    array(
                        'id' => 'item2',
                        'title' => 'Item 2',
                        'action' => 'adminhtml/controller/item2'
                    )
                ),
                array(
                    'id' => 'item1',
                    'title' => 'Item 1',
                    'action' => 'adminhtml/controller/item1'
                )
            ),
            array(
                array(
                    array(
                        'id' => 'Namespace_Module::item1',
                        'title' => 'Item 1',
                        'action' => 'adminhtml/controller/item1'
                    ),
                    array(
                        'id' => 'Namespace_Module::item2',
                        'title' => 'Item 2',
                        'action' => 'adminhtml/controller/item2'
                    )
                ),
                array(
                    'id' => 'Namespace_Module::item1',
                    'title' => 'Item 1',
                    'action' => 'adminhtml/controller/item1'
                )
            )
        );
    }
}

