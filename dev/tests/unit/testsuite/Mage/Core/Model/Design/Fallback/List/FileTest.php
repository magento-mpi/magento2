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

class Mage_Core_Model_Design_Fallback_List_FileTest extends PHPUnit_Framework_TestCase
{
    public function testGetFallbackRules()
    {
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $model = new Mage_Core_Model_Design_Fallback_List_File($dirs);

        $method = new ReflectionMethod($model, '_getFallbackRules');
        $method->setAccessible(true);
        $actualResult = $method->invoke($model);
        $this->assertCount(2, $actualResult);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Theme', $actualResult[0]);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Simple', $actualResult[1]);
    }
}
