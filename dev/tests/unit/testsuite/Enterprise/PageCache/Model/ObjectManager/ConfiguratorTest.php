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

        $expectedParams = array(
            'Enterprise_PageCache_Model_Cache' => array(
                'parameters' => array('config' => 'Mage_Core_Model_Config_Proxy')
            ),
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => 'run_code'),
            ));
        $objectManager->expects($this->once())->method('setConfiguration')->with($expectedParams);
        $this->_model->configure($objectManager);
    }

}
