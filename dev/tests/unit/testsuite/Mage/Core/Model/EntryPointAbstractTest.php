<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_EntryPointAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested: successful model creation, verification of directories, call of template method _processRequest()
     *
     * @magentoAppIsolation enabled
     */
    public function testProcessRequest()
    {
        $dirVerification = $this->getMock('Mage_Core_Model_Dir_Verification', array(), array(), '', false);
        $dirVerification->expects($this->once())
            ->method('createAndVerifyDirectories');

        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Dir_Verification')
            ->will($this->returnValue($dirVerification));

        $config = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);

        $model = $this->getMockForAbstractClass('Mage_Core_Model_EntryPointAbstract',
            array($config, $objectManager), '');
        $model->expects($this->once())
            ->method('_processRequest');
        $model->processRequest();
    }
}
