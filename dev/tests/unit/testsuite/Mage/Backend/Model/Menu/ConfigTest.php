<?php

class Mage_Backend_Model_Menu_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Mode_Config
     */
    protected $_appConfigMock;

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheInstanceMock;

    /**
     * @var DOMDocument
     */
    protected $_domDocumentMock;

    /**
     * @var Mage_Backend_Model_Menu_Director_Dom
     */
    protected $_directorDomMock;

    /**
     * @var Mage_Backend_Model_Menu_Config_Menu
     */
    protected $_configMenuMock;

    /**
     * @var Mage_Backend_Model_Menu_Builder
     */
    protected $_builderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_itemFactoryMock;

    /**
     * @var Mage_Backend_Model_Menu_Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_appConfigMock->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback(array($this, 'getModelInstance')));

        $this->_cacheInstanceMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);

        $this->_directorDomMock = $this->getMock('Mage_Backend_Model_Menu_Director_Dom', array(), array(), '', false);

        $this->_builderMock = $this->getMock('Mage_Backend_Model_Menu_Builder', array(), array(), '', false);

        $this->_configMenuMock = $this->getMock('Mage_Backend_Model_Menu_Config_Menu', array(), array(), '', false);

        $this->_domDocumentMock = $this->getMock('DOMDocument', array(), array(), '', false);

        $this->_eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager');

        $this->_model = new Mage_Backend_Model_Menu_Config(array(
            'appConfig' => $this->_appConfigMock,
            'cache' => $this->_cacheInstanceMock,
            'eventManager' => $this->_eventManagerMock,
            'menuBuilder' => $this->_builderMock
        ));
    }

    public function testGetMenuConfigurationFiles()
    {
        $this->_appConfigMock->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                realpath(__DIR__) . '/../_files/menu_1.xml',
                realpath(__DIR__) . '/../_files/menu_2.xml'
            )
        ));
        $this->assertNotEmpty($this->_model->getMenuConfigurationFiles());
    }

    /**
     * @covers Mage_Backend_Model_Menu_Config::getMenu
     */
    public function testGetMenuWhenEnabledCache()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>';

        $this->_cacheInstanceMock->expects($this->any())
            ->method('canUse')
            ->with($this->equalTo('config'))
            ->will($this->returnValue(true));

        $this->_cacheInstanceMock->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($xmlString));

        $this->_directorDomMock->expects($this->exactly(1))
            ->method('buildMenu')
            ->with($this->isInstanceOf('Mage_Backend_Model_Menu_Builder'));

        $this->_builderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->getMock('Mage_Backend_Model_Menu')));

        $this->_model->getMenu();

        /*
         * Recall the same method to ensure that built menu cached in local protected property
         */
        $this->_model->getMenu();
    }

    /**
     * @covers Mage_Backend_Model_Menu_Config::getMenu
     */
    public function testGetMenuWhenDisabledCache()
    {
        $this->_cacheInstanceMock->expects($this->any())
            ->method('canUse')
            ->will($this->returnValue(false));

        $this->_configMenuMock->expects($this->exactly(1))
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_domDocumentMock->expects($this->exactly(1))
            ->method('saveXML')
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>'));

        $this->_model->getMenu();
    }

    /**
     * @covers Mage_Backend_Model_Menu_Config::getMenu
     */
    public function testGetMenuWhenCacheEnabledAndCleaned()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>';

        $this->_appConfigMock->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback(array($this, 'getModelInstance')));

        $this->_cacheInstanceMock->expects($this->any())
            ->method('canUse')
            ->will($this->returnValue(true));

        $this->_cacheInstanceMock->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue(null));

        $this->_domDocumentMock->expects($this->exactly(1))
            ->method('saveXML')
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>'));

        $this->_configMenuMock->expects($this->exactly(1))
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_cacheInstanceMock->expects($this->exactly(1))
            ->method('save')
            ->with($this->equalTo($xmlString));

        $this->_model->getMenu();
    }

    public function testGetMenuTriggersEventOnlyOnceAfterMenuIsCreated()
    {
        $menuMock = new Varien_Object();
        $this->_eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('backend_menu_load_after'), $this->equalTo(array('menu' => $menuMock)));

        $this->_builderMock->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($menuMock));

        $this->_configMenuMock->expects($this->once())
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_model->getMenu();
        $this->_model->getMenu();
    }

    /**
     * Callback method for mock object Mage_Core_Model_Config object
     *
     * @param mixed $model
     * @param mixed $arguments
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getModelInstance($model, $arguments)
    {
        if ($model == 'Mage_Backend_Model_Menu_Director_Dom') {
            return $this->_directorDomMock;
        } elseif ($model == 'Mage_Backend_Model_Menu_Config_Menu') {
            return $this->_configMenuMock;
        } else {
            return $this->getMock($model, array(), $arguments, '', false);
        }
    }
}
