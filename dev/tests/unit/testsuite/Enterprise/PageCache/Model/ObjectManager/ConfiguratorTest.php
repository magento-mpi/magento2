<?php
    /**
     * {license_notice}
     *
     *
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * Test class for Enterprise_PageCache_Model_ObjectManager_Configurator
     */
class Enterprise_PageCache_Model_ObjectManager_ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_ObjectManager_Configurator
     */
    protected $_model;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->_model = new Enterprise_PageCache_Model_ObjectManager_Configurator();
    }

    public function testConfigure()
    {
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false, false);
        $runTimeParams = array('runCode' => 'test_code');

        $expectedParams = array(
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => 'test_code'),
            ));
        $objectManager->expects($this->once())->method('configure')->with($expectedParams);
        $this->_model->configure($objectManager, $runTimeParams);
    }

}
