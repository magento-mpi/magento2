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
     * @covers \Magento\Core\Model\Theme\Domain\Staging::updateFromStagingTheme
     */
    public function testUpdateFromStagingTheme()
    {
        $parentTheme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false, false);

        $theme = $this->getMock('Magento\Core\Model\Theme', array('getParentTheme'), array(), '', false, false);
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $themeCopyService = $this->getMock('Magento\Core\Model\Theme\CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->once())->method('copy')->with($theme, $parentTheme);

        $object = new \Magento\Core\Model\Theme\Domain\Staging($theme, $themeCopyService);
        $this->assertSame($object, $object->updateFromStagingTheme());
    }
}
