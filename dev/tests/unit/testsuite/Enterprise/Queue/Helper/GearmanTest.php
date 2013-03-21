<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Queue_Helper_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $arguments
     * @return Enterprise_Queue_Helper_Gearman
     */
    protected function _getHelperGearman($arguments = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Enterprise_Queue_Helper_Gearman', $arguments);
    }

    public function testGetServers()
    {
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $configMock->expects($this->once())->method('getNode')
            ->with(Enterprise_Queue_Helper_Gearman::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS)
            ->will($this->returnValue('127.0.0.1:4730'));

        $helperGearman = $this->_getHelperGearman(array(
            'config' => $configMock
        ));

        $this->assertEquals('127.0.0.1:4730', $helperGearman->getServers());
    }

    public function testPrepareData()
    {
        $helperGearman = $this->_getHelperGearman();

        $data = array(
            'foo' => array(
                'baz' => 'bar'
            ),
            'qux' => 123,
            2 => true
        );
        $this->assertEquals(json_encode($data), $helperGearman->prepareData($data));
    }
}
