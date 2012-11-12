<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Validator_StringLength
 */
class Magento_Validator_StringLengthTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Validator_StringLength
     */
    protected $_validator;

    protected function setUp()
    {
        $this->_validator = new Magento_Validator_StringLength();
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
            array('string', 5, false),
        );
    }
}
