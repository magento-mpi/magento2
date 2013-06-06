<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_BusyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Block_Adminhtml_Import_Busy
     */
    protected $_block;

    public function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Saas_ImportExport_Block_Adminhtml_Import_Busy');
    }

    /**
     * @return array
     */
    public function dataProviderForSetGetStatusMessage()
    {
        return array(
            array('message1'),
            array('message2'),
        );
    }

    /**
     * @param string $message
     * @dataProvider dataProviderForSetGetStatusMessage
     */
    public function testSetGetStatusMessage($message)
    {
        $this->_block->setStatusMessage($message);
        $this->assertEquals($message, $this->_block->getStatusMessage());
    }
}
