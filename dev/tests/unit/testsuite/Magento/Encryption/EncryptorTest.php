<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Encryption;

class EncryptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Encryption\Encryptor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_randomGenerator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cryptFactory;

    protected function setUp()
    {
        $this->_randomGenerator = $this->getMock('Magento\Math\Random', array(), array(), '', false);
        $this->_cryptFactory = $this->getMock('Magento\Encryption\CryptFactory', array(), array(), '', false);
        $this->_model = new Encryptor($this->_randomGenerator, $this->_cryptFactory, 'cryptKey');
    }

    public function testGetHashNoSalt()
    {
        $this->_randomGenerator->expects($this->never())->method('getRandomString');
        $expected = '5f4dcc3b5aa765d61d8327deb882cf99';
        $actual = $this->_model->getHash('password');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashSpecifiedSalt()
    {
        $this->_randomGenerator->expects($this->never())->method('getRandomString');
        $expected = '67a1e09bb1f83f5007dc119c14d663aa:salt';
        $actual = $this->_model->getHash('password', 'salt');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashRandomSaltDefaultLength()
    {
        $this->_randomGenerator
            ->expects($this->once())
            ->method('getRandomString')
            ->with(32)
            ->will($this->returnValue('-----------random_salt----------'))
        ;
        $expected = '7a22dd7ba57a7653cc0f6e58e9ba1aac:-----------random_salt----------';
        $actual = $this->_model->getHash('password', true);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashRandomSaltSpecifiedLength()
    {
        $this->_randomGenerator
            ->expects($this->once())
            ->method('getRandomString')
            ->with(11)
            ->will($this->returnValue('random_salt'))
        ;
        $expected = 'e6730b5a977c225a86cd76025a86a6fc:random_salt';
        $actual = $this->_model->getHash('password', 11);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $password
     * @param string $hash
     * @param bool $expected
     *
     * @dataProvider validateHashDataProvider
     */
    public function testValidateHash($password, $hash, $expected)
    {
        $actual = $this->_model->validateHash($password, $hash);
        $this->assertEquals($expected, $actual);
    }

    public function validateHashDataProvider()
    {
        return array(
            array('password', 'hash', false),
            array('password', 'hash:salt', false),
            array('password', '5f4dcc3b5aa765d61d8327deb882cf99', true),
            array('password', '67a1e09bb1f83f5007dc119c14d663aa:salt', true),
        );
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid hash.
     * @dataProvider validateHashExceptionDataProvider
     */
    public function testValidateHashException($password, $hash)
    {
        $this->_model->validateHash($password, $hash);
    }

    public function validateHashExceptionDataProvider()
    {
        return array(
            array('password', 'hash1:hash2:hash3'),
            array('password', 'hash1:hash2:hash3:hash4'),
        );
    }

    /**
     * @param mixed $key
     *
     * @dataProvider encryptWithEmptyKeyDataProvider
     */
    public function testEncryptWithEmptyKey($key)
    {
        $encryptor = new \Magento\Encryption\Encryptor(
            $this->getMock('\Magento\Math\Random', array(), array(), '', false),
            $this->getMock('Magento\Encryption\CryptFactory', array(), array(), '', false),
            $key
        );

        $value = 'arbitrary_string';

        $this->assertEquals($value, $encryptor->encrypt($value));
    }

    public function encryptWithEmptyKeyDataProvider()
    {
        return array(
            array(null),
            array(0),
            array(''),
            array('0'),
        );
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider encryptDataProvider
     */
    public function testEncrypt($value, $expected)
    {
        $crypt = $this->getMock('Magento\Encryption\Crypt', array(), array(), '', false);
        $this->_cryptFactory->expects($this->once())->method('create')->will($this->returnValue($crypt));
        $crypt->expects($this->once())->method('encrypt')->with($value)->will($this->returnArgument(0));
        $actual = $this->_model->encrypt($value);
        $this->assertEquals($expected, $actual);
    }

    public function encryptDataProvider()
    {
        return array(
            array('value1', 'dmFsdWUx'),
            array(true, 'MQ=='),
        );
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider decryptDataProvider
     */
    public function testDecrypt($value, $expected)
    {
        $crypt = $this->getMock('Magento\Encryption\Crypt', array(), array(), '', false);
        $this->_cryptFactory->expects($this->once())->method('create')->will($this->returnValue($crypt));
        $crypt->expects($this->once())->method('decrypt')->with($expected)->will($this->returnValue($expected));
        $actual = $this->_model->decrypt($value);
        $this->assertEquals($expected, $actual);
    }

    public function decryptDataProvider()
    {
        return array(
            array(base64_encode('value1'), 'value1'),
        );
    }

    public function testValidateKey()
    {
        $crypt = $this->getMock('Magento\Encryption\Crypt', array(), array(), '', false);
        $this->_cryptFactory->expects($this->once())
            ->method('create')
            ->with(array('key' => 'some_key'))
            ->will($this->returnValue($crypt))
        ;
        $this->_model->validateKey('some_key');
    }

    public function testValidateKeyDefault()
    {
        $crypt = $this->getMock('Magento\Encryption\Crypt', array(), array(), '', false);
        $this->_cryptFactory->expects($this->once())
            ->method('create')
            ->with(array('key' => 'cryptKey'))
            ->will($this->returnValue($crypt))
        ;
        $this->_model->validateKey(null);
    }
}
