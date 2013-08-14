<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme staging model
 */
class Magento_Core_Model_Theme_Domain_StagingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Core_Model_Theme_Domain_Staging::updateFromStagingTheme
     */
    public function testUpdateFromStagingTheme()
    {
        $parentTheme = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false, false);

        $theme = $this->getMock('Magento_Core_Model_Theme', array('getParentTheme'), array(), '', false, false);
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $themeCopyService = $this->getMock('Magento_Core_Model_Theme_CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->once())->method('copy')->with($theme, $parentTheme);

        $object = new Magento_Core_Model_Theme_Domain_Staging($theme, $themeCopyService);
        $this->assertSame($object, $object->updateFromStagingTheme());
    }
}
