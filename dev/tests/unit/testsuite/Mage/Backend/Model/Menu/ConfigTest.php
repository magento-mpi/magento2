<?php

class Mage_Backend_Model_Menu_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Config
     */
    protected $_model;

    public function setUp()
    {
        $appConfig = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $appConfig->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                realpath(__DIR__) . '/_files/menu_1.xml',
                realpath(__DIR__) . '/_files/menu_2.xml'
            )));

        $this->_model = new Mage_Backend_Model_Menu_Config(array(
            'appConfig' => $appConfig
        ));
    }

    public function testGetMenuConfigurationFiles()
    {
        $this->assertNotEmpty($this->_model->getMenuConfigurationFiles());
    }
}
