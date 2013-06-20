<?php
/**
 * Test class for Enterprise_PageCache_Model_ObjectManager_Configurator
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ObjectManager_ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_ObjectManager_Configurator
     */
    protected $_model;

    protected function setUp()
    {
        $params = array(
            Mage::PARAM_RUN_CODE => 'run_code',
        );
        $this->_model = new Enterprise_PageCache_Model_ObjectManager_Configurator($params);
    }

    public function testConfigure()
    {
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false, false);
        $objectManager->expects($this->once())->method('configure');
        $this->_model->configure($objectManager);
    }

}
