<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Invalidator
 */
class Mage_Core_Model_Config_InvalidatorTest extends PHPUnit_Framework_TestCase
{
   public function testInvalidate()
   {
       $primaryMock = $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false, false);
       $modulesMock = $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false, false);
       $localesMock = $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false, false);
       $model = new Mage_Core_Model_Config_Invalidator($primaryMock, $modulesMock, $localesMock);

       $primaryMock->expects($this->once())->method('reinit');
       $modulesMock->expects($this->once())->method('reinit');
       $localesMock->expects($this->once())->method('reinit');
       $model->invalidate();
   }
}
