<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_EntryPointAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested: successful model creation, verification of directories, call of template method _processRequest()
     *
     * @magentoAppIsolation enabled
     */
    public function testProcessRequest()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');

        $config = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);

        $model = $this->getMockForAbstractClass('Magento_Core_Model_EntryPointAbstract',
            array($config, $objectManager), '');
        $model->expects($this->once())
            ->method('_processRequest');
        $model->processRequest();
    }
}
