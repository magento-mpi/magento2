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

class Mage_Backend_Model_Menu_Item_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item_Factory
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
     * @var PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $_helpers = array();

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_itemValidatorMock;

    /**
     * Constructor params
     *
     * @var array
     */
    protected $_params = array();

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
        $this->_factoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helpers = array(
            'Mage_Backend_Helper_Data' => $this->getMock('Mage_Backend_Helper_Data'),
            'Mage_User_Helper_Data' => $this->getMock('Mage_User_Helper_Data')
        );
        $this->_urlModelMock = $this->getMock("Mage_Backend_Model_Url", array(), array(), '', false);
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config');
        $this->_itemValidatorMock = $this->getMock('Mage_Backend_Model_Menu_Item_Validator');

        $this->_params = array(
            'acl' => $this->_aclMock,
            'objectFactory' => $this->_factoryMock,
            'helpers' => $this->_helpers,
            'urlModel' => $this->_urlModelMock,
            'appConfig' => $this->_appConfigMock,
            'storeConfig' => $this->_storeConfigMock,
            'validator' => $this->_itemValidatorMock
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresAcl()
    {
        unset($this->_params['acl']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresObjectFactory()
    {
        unset($this->_params['objectFactory']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresUrlModel()
    {
        unset($this->_params['urlModel']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresAppConfig()
    {
        unset($this->_params['appConfig']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresStoreConfig()
    {
        unset($this->_params['storeConfig']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresItemValidator()
    {
        unset($this->_params['validator']);
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    public function testCreateFromArray()
    {
        $this->_factoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'module' => $this->_helpers['Mage_User_Helper_Data'],
                    'dependsOnModule' => 'Mage_User_Helper_Data',
                    'acl' => $this->_aclMock,
                    'objectFactory' => $this->_factoryMock,
                    'urlModel' => $this->_urlModelMock,
                    'appConfig' => $this->_appConfigMock,
                    'storeConfig' => $this->_storeConfigMock
                ))
        );
        $model = new Mage_Backend_Model_Menu_Item_Factory($this->_params);
        $model->createFromArray(array(
            'module' => 'Mage_User_Helper_Data',
            'dependsOnModule' => 'Mage_User_Helper_Data'
        ));
    }

    public function testCreateFromArrayProvidesDefaultHelper()
    {
        $this->_factoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
            $this->equalTo('Mage_Backend_Model_Menu_Item'),
            $this->equalTo(array(
                'module' => $this->_helpers['Mage_Backend_Helper_Data'],
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_factoryMock,
                'urlModel' => $this->_urlModelMock,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock
            ))
        );
        $model = new Mage_Backend_Model_Menu_Item_Factory($this->_params);
        $model->createFromArray(array());
    }
}
