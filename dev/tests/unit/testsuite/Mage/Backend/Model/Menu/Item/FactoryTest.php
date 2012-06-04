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
     * @var Mage_Core_Model_Config
     */
    protected $_factoryMock;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_aclMock;

    /**
     * @var Mage_Core_Model_Helper[]
     */
    protected $_helpers = array();

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfigMock;

    /**
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfigMock;

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
        $this->_factoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helpers = array(
            'Mage_Backend_Helper_Data' => $this->getMock('Mage_Backend_Helper_Data'),
            'Mage_User_Helper_Data' => $this->getMock('Mage_User_Helper_Data')
        );
        $this->_urlModel = $this->getMock("Mage_Backend_Model_Url", array(), array(), '', false);
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config');

        $this->_model = new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers,
                'urlModel' => $this->_urlModel,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock

            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresAcl()
    {
        new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_factoryMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers,
                'urlModel' => $this->_urlModel,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresObjectFactory()
    {
        new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_aclMock,
                'helpers' => $this->_helpers,
                'urlModel' => $this->_urlModel,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock

            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresUrlModel()
    {
        new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresAppConfig()
    {
        new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers,
                'urlModel' => $this->_urlModel,
                'storeConfig' => $this->_storeConfigMock
            )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresStoreConfig()
    {
        new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $this->_aclMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers,
                'urlModel' => $this->_urlModel,
                'appConfig' => $this->_appConfigMock,
            )
        );
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
                    'urlModel' => $this->_urlModel,
                    'appConfig' => $this->_appConfigMock,
                    'storeConfig' => $this->_storeConfigMock
                ))
        );
        $this->_model->createFromArray(array(
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
                'urlModel' => $this->_urlModel,
                'appConfig' => $this->_appConfigMock,
                'storeConfig' => $this->_storeConfigMock
            ))
        );
        $this->_model->createFromArray(array());
    }
}
