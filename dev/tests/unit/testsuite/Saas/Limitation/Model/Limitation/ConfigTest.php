<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Limitation_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGetThreshold()
    {
        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->once())->method('getNode')->with('limitations/test')->will($this->returnValue('5'));

        $model = new Saas_Limitation_Model_Limitation_Config($config);
        $this->assertSame(5, $model->getThreshold('test'));
    }
}
