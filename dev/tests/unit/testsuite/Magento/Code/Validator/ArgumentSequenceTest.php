<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

require_once ('_files/ClassesForArgumentSequence.php');

class ArgumentSequenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Validator\ArgumentSequence
     */
    protected $_validator;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_fixturePath = realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'ClassesForArgumentSequence.php';
        $this->_validator = $this->getMock('Magento\Code\Validator\ArgumentSequence', array('_isAllowedType'));
        $this->_validator->expects($this->any())->method('_isAllowedType')->will($this->returnValue(true));
    }

    public function testValidSequence()
    {
        $this->assertTrue($this->_validator->validate('\ArgumentSequence\ValidChildClass'));
    }

    public function testInvalidSequence()
    {
        $expectedSequence = '$contextObject, $parentRequiredObject, $parentRequiredScalar, '
            . '$childRequiredObject, $childRequiredScalar, $parentOptionalObject, $data, $parentOptionalScalar, '
            . '$childOptionalObject, $childOptionalScalar';

        $actualSequence = '$contextObject, $childRequiredObject, $parentRequiredObject, $parentRequiredScalar, '
            . '$childRequiredScalar, $parentOptionalObject, $data, $parentOptionalScalar, '
            . '$childOptionalObject, $childOptionalScalar';

        $message = 'Incorrect argument sequence in class %s in ' . $this->_fixturePath . PHP_EOL
            . 'Required: %s' . PHP_EOL . 'Actual  : %s' . PHP_EOL;
        $message = sprintf($message, '\ArgumentSequence\InvalidChildClass', $expectedSequence, $actualSequence);
        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_validator->validate('\ArgumentSequence\InvalidChildClass');
    }
}