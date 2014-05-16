<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator;

/**
 * Test case for \Magento\Framework\Validator\StringLength
 */
class StringLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Validator\StringLength
     */
    protected $_validator;

    protected function setUp()
    {
        $this->_validator = new \Magento\Framework\Validator\StringLength();
    }

    public function testDefaultEncoding()
    {
        $this->assertEquals('UTF-8', $this->_validator->getEncoding());
    }

    /**
     * @dataProvider isValidDataProvider
     * @param string $value
     * @param int $maxLength
     * @param bool $isValid
     */
    public function testIsValid($value, $maxLength, $isValid)
    {
        $this->_validator->setMax($maxLength);
        $this->assertEquals($isValid, $this->_validator->isValid($value));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            array('строка', 6, true),
            array('строка', 5, false),
            array('string', 6, true),
            array('string', 5, false)
        );
    }
}
