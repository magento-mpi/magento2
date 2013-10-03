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
namespace Magento\Core\Model\Theme\Domain;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Core\Model\Theme\Domain\Factory::create
     */
    public function testCreate()
    {
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(\Magento\Core\Model\Theme::TYPE_VIRTUAL));

        $newThemeMock = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Theme\Domain\Virtual', array('theme' => $themeMock))
            ->will($this->returnValue($newThemeMock));

        $themeDomainFactory = new \Magento\Core\Model\Theme\Domain\Factory($objectManager);
        $this->assertEquals($newThemeMock, $themeDomainFactory->create($themeMock));
    }

    /**
     * @covers \Magento\Core\Model\Theme\Domain\Factory::create
     */
    public function testCreateWithWrongThemeType()
    {
        $wrongThemeType = 'wrong_theme_type';
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($wrongThemeType));

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);

        $themeDomainFactory = new \Magento\Core\Model\Theme\Domain\Factory($objectManager);

        $this->setExpectedException(
            'Magento\Core\Exception',
            sprintf('Invalid type of theme domain model "%s"', $wrongThemeType)
        );
        $themeDomainFactory->create($themeMock);
    }
}
