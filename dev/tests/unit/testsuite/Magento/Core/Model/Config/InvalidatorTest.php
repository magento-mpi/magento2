<?php
/**
 * Test class for Magento_Core_Model_Config_Invalidator
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_InvalidatorTest extends PHPUnit_Framework_TestCase
{
    public function testInvalidate()
    {
        $primaryMock = $this->getMock('Magento_Core_Model_ConfigInterface', array(), array(), '', false, false);
        $modulesMock = $this->getMock('Magento_Core_Model_ConfigInterface', array(), array(), '', false, false);
        $localesMock = $this->getMock('Magento_Core_Model_ConfigInterface', array(), array(), '', false, false);
        $model = new Magento_Core_Model_Config_Invalidator($primaryMock, $modulesMock, $localesMock);

        $primaryMock->expects($this->once())->method('reinit');
        $modulesMock->expects($this->once())->method('reinit');
        $localesMock->expects($this->once())->method('reinit');
        $model->invalidate();
    }
}
