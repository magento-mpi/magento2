<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Helper_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Enterprise_Queue_Helper_Gearman
     */
    protected $_helperGearman;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helperGearman = $objectManagerHelper->getObject('Enterprise_Queue_Helper_Gearman', array(
            'config' => $this->_configMock,
        ));
    }

    public function testGetServers()
    {
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Enterprise_Queue_Helper_Gearman::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS)
            ->will($this->returnValue('127.0.0.1:4730'));

        $this->assertEquals('127.0.0.1:4730', $this->_helperGearman->getServers());
    }

    public function testEncodeData()
    {
        $data = array(
            'foo' => array(
                'baz' => 'bar',
            ),
            'qux' => 123,
            2 => true,
        );
        $this->assertEquals(json_encode($data), $this->_helperGearman->encodeData($data));
    }
}
