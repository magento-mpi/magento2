<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_File_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_File_Factory
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager');
        $this->_model = new Mage_Core_Model_Layout_File_Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $file = new Mage_Core_Model_Layout_File(__FILE__, 'Fixture_Module', $theme);
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with(
                'Mage_Core_Model_Layout_File',
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
