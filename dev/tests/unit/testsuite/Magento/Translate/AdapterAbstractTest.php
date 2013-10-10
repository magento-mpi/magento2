<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translate;

class AdapterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Translate\AbstractAdapter
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento\Translate\AbstractAdapter')
            ->getMockForAbstractClass();
    }

    /**
     * Magento translate adapter should always return false to be used correctly be Zend Validate
     */
    public function testIsTranslated()
    {
        $this->assertFalse($this->_model->isTranslated('string'));
    }

    /**
     * Test set locale do nothing
     */
    public function testSetLocale()
    {
        $this->assertInstanceOf('Magento\Translate\AbstractAdapter', $this->_model->setLocale('en_US'));
    }

    /**
     * Check that abstract method is implemented
     */
    public function testToString()
    {
        $this->assertEquals('Magento\Translate\Adapter', $this->_model->toString());
    }
}
