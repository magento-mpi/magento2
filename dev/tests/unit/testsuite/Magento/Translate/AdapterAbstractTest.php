<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Translate_AdapterAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Translate_AdapterAbstract
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento_Translate_AdapterAbstract')
            ->getMockForAbstractClass();
    }

    /**
     * Magento translate adapter should always have translation
     */
    public function testIsTranslated()
    {
        $this->assertTrue($this->_model->isTranslated('string'));
    }

    /**
     * Test set locale do nothing
     */
    public function testSetLocale()
    {
        $this->assertInstanceOf('Magento_Translate_AdapterAbstract', $this->_model->setLocale('en_US'));
    }

    /**
     * Check that abstract method is implemented
     */
    public function testToString()
    {
        $this->assertEquals('Magento_Translate_Adapter', $this->_model->toString());
    }
}
