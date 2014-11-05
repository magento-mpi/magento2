<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Customer\Attribute\Backend;

use Magento\Framework\Stdlib\String;

class PasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Password
     */
    protected $testable;

    public function setUp()
    {
        $string = new String();
        $this->testable = new Password($string);
    }

    public function testValidatePositive()
    {
        $password = 'password';
        $object = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(array('getPassword', 'getPasswordConfirm'))
            ->getMock();

        $object->expects($this->once())->method('getPassword')->will($this->returnValue($password));
        $object->expects($this->once())->method('getPasswordConfirm')->will($this->returnValue($password));
        /** @var \Magento\Framework\Object $object */

        $this->assertTrue($this->testable->validate($object));
    }

    public function passwordNegativeDataProvider()
    {
        return array(
            'less-then-6-char' => array('less6'),
            'with-space-prefix' => array(' normal_password'),
            'with-space-suffix' => array('normal_password '),
        );
    }

    /**
     * @dataProvider passwordNegativeDataProvider
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testBeforeSaveNegative($password)
    {
        $object = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(array('getPassword'))
            ->getMock();

        $object->expects($this->once())->method('getPassword')->will($this->returnValue($password));
        /** @var \Magento\Framework\Object $object */

        $this->testable->beforeSave($object);
    }

    public function testBeforeSavePositive()
    {
        $password = 'more-then-6';
        $passwordHash = 'password-hash';
        $object = $this->getMockBuilder('Magento\Framework\Object')
            ->disableOriginalConstructor()
            ->setMethods(array('getPassword', 'setPasswordHash', 'hashPassword'))
            ->getMock();

        $object->expects($this->once())->method('getPassword')->will($this->returnValue($password));
        $object->expects($this->once())->method('hashPassword')->will($this->returnValue($passwordHash));
        $object->expects($this->once())->method('setPasswordHash')->with($passwordHash)->will($this->returnSelf());
        /** @var \Magento\Framework\Object $object */

        $this->testable->beforeSave($object);
    }
}
