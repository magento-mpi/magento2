<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Magento_Data_Form_Element_Factory
 */
class Magento_Data_Form_Element_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_factory;

    public function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager_ObjectManager',
            array('create'), array(), '', false);
        $this->_factory = new Magento_Data_Form_Element_Factory($this->_objectManagerMock);
    }

    /**
     * @param string $type
     * @dataProvider createPositiveDataProvider
     */
    public function testCreatePositive($type)
    {
        $className = 'Magento_Data_Form_Element_' . ucfirst($type);
        $elementMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, array())
            ->will($this->returnValue($elementMock));
        $this->assertSame($elementMock, $this->_factory->create($type));
    }

    /**
     * @param string $type
     * @dataProvider createPositiveDataProvider
     */
    public function testCreatePositiveWithNotEmptyConfig($type)
    {
        $config = array('attributes' => array('attr1' => 'attr1', 'attr2' => 'attr2'));
        $className = 'Magento_Data_Form_Element_' . ucfirst($type);
        $elementMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $config)
            ->will($this->returnValue($elementMock));
        $this->assertSame($elementMock, $this->_factory->create($type, $config));
    }

    /**
     * @return array
     */
    public function createPositiveDataProvider()
    {
        return array(
            'button' => array('button'),
            'checkbox' => array('checkbox'),
            'checkboxes' => array('checkboxes'),
            'column' => array('column'),
            'date' => array('date'),
            'editablemultiselect' => array('editablemultiselect'),
            'editor' => array('editor'),
            'fieldset' => array('fieldset'),
            'file' => array('file'),
            'gallery' => array('gallery'),
            'hidden' => array('hidden'),
            'image' => array('image'),
            'imagefile' => array('imagefile'),
            'label' => array('label'),
            'link' => array('link'),
            'multiline' => array('multiline'),
            'multiselect' => array('multiselect'),
            'note' => array('note'),
            'obscure' => array('obscure'),
            'password' => array('password'),
            'radio' => array('radio'),
            'radios' => array('radios'),
            'reset' => array('reset'),
            'select' => array('select'),
            'submit' => array('submit'),
            'text' => array('text'),
            'textarea' => array('textarea'),
            'time' => array('time'),
        );
    }

    /**
     * @param string $type
     * @dataProvider createExceptionReflectionExceptionDataProvider
     * @expectedException ReflectionException
     */
    public function testCreateExceptionReflectionException($type)
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($type, array())
            ->will($this->throwException(new ReflectionException()));
        $this->_factory->create($type);
    }

    /**
     * @return array
     */
    public function createExceptionReflectionExceptionDataProvider()
    {
        return array(
            'factory' => array('factory'),
            'collection' => array('collection'),
            'abstract' => array('abstract'),
        );
    }

    /**
     * @param string $type
     * @dataProvider createExceptionInvalidArgumentDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testCreateExceptionInvalidArgument($type)
    {
        $elementMock = $this->getMock($type, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($type, array())
            ->will($this->returnValue($elementMock));
        $this->_factory->create($type);
    }

    /**
     * @return array
     */
    public function createExceptionInvalidArgumentDataProvider()
    {
        return array(
            'Magento_Data_Form_Element_Factory' => array('Magento_Data_Form_Element_Factory'),
            'Magento_Data_Form_Element_Collection' => array('Magento_Data_Form_Element_Collection'),
        );
    }
}
