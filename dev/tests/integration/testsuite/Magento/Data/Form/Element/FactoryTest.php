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
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_factory;

    public function setUp()
    {
        $this->_factory = Mage::getObjectManager()->get('Magento_Data_Form_Element_Factory');
    }

    /**
     * @param string $type
     * @dataProvider createPositiveDataProvider
     */
    public function testCreatePositive($type)
    {
        $element = $this->_factory->create($type);
        $this->assertInstanceOf('Magento_Data_Form_Element_' . ucfirst($type), $element);
    }

    /**
     * @return array
     */
    public function createPositiveDataProvider()
    {
        return array(
            array('button'),
            array('checkbox'),
            array('checkboxes'),
            array('column'),
            array('date'),
            array('editablemultiselect'),
            array('editor'),
            array('fieldset'),
            array('file'),
            array('gallery'),
            array('hidden'),
            array('image'),
            array('imagefile'),
            array('label'),
            array('link'),
            array('multiline'),
            array('multiselect'),
            array('note'),
            array('obscure'),
            array('password'),
            array('radio'),
            array('radios'),
            array('reset'),
            array('select'),
            array('submit'),
            array('text'),
            array('textarea'),
            array('time'),
        );
    }

    /**
     * @param string $type
     * @dataProvider createExceptionReflectionExceptionDataProvider
     * @expectedException ReflectionException
     */
    public function testCreateExceptionReflectionException($type)
    {
        $this->_factory->create($type);
    }

    /**
     * @return array
     */
    public function createExceptionReflectionExceptionDataProvider()
    {
        return array(
            array('factory'),
            array('collection'),
            array('abstract'),
        );
    }

    /**
     * @param string $type
     * @dataProvider createExceptionInvalidArgumentDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testCreateExceptionInvalidArgument($type)
    {
        $this->_factory->create($type);
    }

    /**
     * @return array
     */
    public function createExceptionInvalidArgumentDataProvider()
    {
        return array(
            array('Magento_Data_Form_Element_Factory'),
            array('Magento_Data_Form_Element_Collection'),
        );
    }
}
