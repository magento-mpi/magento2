<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for \Magento\Validator\ValidatorAbstract
 */
class Magento_Validator_ValidatorAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|\Magento\Translate\AdapterInterface
     */
    protected $_defaultTranslator = null;

    protected function setUp()
    {
        $this->_defaultTranslator = \Magento\Validator\ValidatorAbstract::getDefaultTranslator();
    }

    protected function tearDown()
    {
        \Magento\Validator\ValidatorAbstract::setDefaultTranslator($this->_defaultTranslator);
    }

    /**
     * Get translator object
     *
     * @return PHPUnit_Framework_MockObject_MockObject|\Magento\Translate\AdapterAbstract
     */
    protected function _getTranslator()
    {
        return $this->getMockBuilder('Magento\Translate\AdapterInterface')
            ->getMockForAbstractClass();
    }

    /**
     * Test default translator get/set
     */
    public function testDefaultTranslatorGetSet()
    {
        $translator = $this->_getTranslator();
        \Magento\Validator\ValidatorAbstract::setDefaultTranslator($translator);
        $this->assertEquals($translator, \Magento\Validator\ValidatorAbstract::getDefaultTranslator());
    }

    /**
     * Test get/set/has translator
     */
    public function testTranslatorGetSetHas()
    {
        /** @var \Magento\Validator\ValidatorAbstract $validator */
        $validator = $this->getMockBuilder('Magento\Validator\ValidatorAbstract')
            ->getMockForAbstractClass();
        $translator = $this->_getTranslator();
        $validator->setTranslator($translator);
        $this->assertEquals($translator, $validator->getTranslator());
        $this->assertTrue($validator->hasTranslator());
    }

    /**
     * Check that default translator returned if set and no translator set
     */
    public function testGetTranslatorDefault()
    {
        /** @var \Magento\Validator\ValidatorAbstract $validator */
        $validator = $this->getMockBuilder('Magento\Validator\ValidatorAbstract')
            ->getMockForAbstractClass();
        $translator = $this->_getTranslator();
        \Magento\Validator\ValidatorAbstract::setDefaultTranslator($translator);
        $this->assertEquals($translator, $validator->getTranslator());
        $this->assertFalse($validator->hasTranslator());
    }
}
