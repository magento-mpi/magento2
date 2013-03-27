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

/**
 * Test theme staging model
 */
class Mage_Core_Model_Theme_Domain_StagingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Core_Model_Theme_Domain_Staging::updateFromStagingTheme
     */
    public function testUpdateFromStagingTheme()
    {
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);
        $copyStV = $this->getMock('Mage_Core_Model_Theme_Copy_StagingToVirtual', array('copy'), array(), '', false);
        $copyStV->expects($this->once())
            ->method('copy')
            ->with($themeMock)
            ->will($this->returnSelf());

        $stagingTheme = new Mage_Core_Model_Theme_Domain_Staging($themeMock, $copyStV);
        $this->assertEquals($stagingTheme, $stagingTheme->updateFromStagingTheme());
    }
}
