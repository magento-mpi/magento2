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
 * Test theme domain model
 */
namespace Magento\View\Design\Theme\Domain;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\View\Design\Theme\Domain\Factory::create
     */
    public function testCreate()
    {
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('__wakeup', 'getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(\Magento\View\Design\ThemeInterface::TYPE_VIRTUAL));

        $newThemeMock = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\View\Design\Theme\Domain\VirtualInterface', array('theme' => $themeMock))
            ->will($this->returnValue($newThemeMock));

        $themeDomainFactory = new \Magento\View\Design\Theme\Domain\Factory($objectManager);
        $this->assertEquals($newThemeMock, $themeDomainFactory->create($themeMock));
    }

    /**
     * @covers \Magento\View\Design\Theme\Domain\Factory::create
     */
    public function testCreateWithWrongThemeType()
    {
        $wrongThemeType = 'wrong_theme_type';
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('__wakeup', 'getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($wrongThemeType));

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);

        $themeDomainFactory = new \Magento\View\Design\Theme\Domain\Factory($objectManager);

        $this->setExpectedException(
            'Magento\Exception',
            sprintf('Invalid type of theme domain model "%s"', $wrongThemeType)
        );
        $themeDomainFactory->create($themeMock);
    }
}
