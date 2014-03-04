<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\File\Factory
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $this->_model = new \Magento\View\File\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
        $file = new \Magento\View\File(__FILE__, 'Fixture_Module', $theme);
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with(
                'Magento\View\File',
                $this->identicalTo(array(
                    'filename' => __FILE__,
                    'module' => 'Fixture_Module',
                    'theme' => $theme,
                ))
            )
            ->will($this->returnValue($file))
        ;
        $this->assertSame($file, $this->_model->create(__FILE__, 'Fixture_Module', $theme));
    }
}
