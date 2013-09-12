<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Factory
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\Layout\File\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $theme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $file = new \Magento\Core\Model\Layout\File(__FILE__, 'Fixture_Module', $theme);
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with(
                'Magento\Core\Model\Layout\File',
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
