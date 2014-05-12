<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme staging model
 */
namespace Magento\Core\Model\Theme\Domain;

class StagingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Core\Model\Theme\Domain\Staging::updateFromStagingTheme
     */
    public function testUpdateFromStagingTheme()
    {
        $parentTheme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false, false);

        $theme = $this->getMock(
            'Magento\Core\Model\Theme',
            array('__wakeup', 'getParentTheme'),
            array(),
            '',
            false,
            false
        );
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $themeCopyService = $this->getMock('Magento\Theme\Model\CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->once())->method('copy')->with($theme, $parentTheme);

        $object = new \Magento\Core\Model\Theme\Domain\Staging($theme, $themeCopyService);
        $this->assertSame($object, $object->updateFromStagingTheme());
    }
}
