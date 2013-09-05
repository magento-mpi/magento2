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
class Magento_Core_Model_Theme_Domain_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Core_Model_Theme_Domain_Factory::create
     */
    public function testCreate()
    {
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array('getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(Magento_Core_Model_Theme::TYPE_VIRTUAL));

        $newThemeMock = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Theme_Domain_Virtual', array('theme' => $themeMock))
            ->will($this->returnValue($newThemeMock));

        $themeDomainFactory = new Magento_Core_Model_Theme_Domain_Factory($objectManager);
        $this->assertEquals($newThemeMock, $themeDomainFactory->create($themeMock));
    }

    /**
     * @covers Magento_Core_Model_Theme_Domain_Factory::create
     */
    public function testCreateWithWrongThemeType()
    {
        $wrongThemeType = 'wrong_theme_type';
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array('getType'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($wrongThemeType));

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);

        $themeDomainFactory = new Magento_Core_Model_Theme_Domain_Factory($objectManager);

        $this->setExpectedException(
            'Magento_Core_Exception',
            sprintf('Invalid type of theme domain model "%s"', $wrongThemeType)
        );
        $themeDomainFactory->create($themeMock);
    }
}
