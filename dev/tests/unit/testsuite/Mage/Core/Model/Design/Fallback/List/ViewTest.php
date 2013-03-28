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

class Mage_Core_Model_Design_Fallback_List_ViewTest extends PHPUnit_Framework_TestCase
{
    public function testGetFallbackRules()
    {
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $model = new Mage_Core_Model_Design_Fallback_List_View($dirs);

        $method = new ReflectionMethod($model, '_getFallbackRules');
        $method->setAccessible(true);
        $actualResult = $method->invoke($model);
        $this->assertCount(4, $actualResult);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Theme', $actualResult[0]);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Simple', $actualResult[1]);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Simple', $actualResult[2]);
        $this->assertInstanceOf('Mage_Core_Model_Design_Fallback_Rule_Simple', $actualResult[3]);
    }
}
