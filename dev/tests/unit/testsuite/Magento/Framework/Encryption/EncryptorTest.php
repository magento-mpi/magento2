<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Encryption;

use Magento\Framework\App\DeploymentConfig;

class EncryptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Encryption\Encryptor
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
        $this->_randomGenerator = $this->getMock('Magento\Framework\Math\Random', [], [], '', false);
        $this->_cryptFactory = $this->getMock('Magento\Framework\Encryption\CryptFactory', [], [], '', false);
        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue('cryptKey'));
        $this->_model = new Encryptor($this->_randomGenerator, $this->_cryptFactory, $deploymentConfigMock);
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
        $this->_randomGenerator->expects(
            $this->once()
        )->method(
            'getRandomString'
        )->with(
            32
        )->will(
            $this->returnValue('-----------random_salt----------')
        );
        $expected = '7a22dd7ba57a7653cc0f6e58e9ba1aac:-----------random_salt----------';
        $actual = $this->_model->getHash('password', true);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashRandomSaltSpecifiedLength()
    {
        $this->_randomGenerator->expects(
            $this->once()
        )->method(
            'getRandomString'
        )->with(
            11
        )->will(
            $this->returnValue('random_salt')
        );
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
        return [
            ['password', 'hash', false],
            ['password', 'hash:salt', false],
            ['password', '5f4dcc3b5aa765d61d8327deb882cf99', true],
            ['password', '67a1e09bb1f83f5007dc119c14d663aa:salt', true]
        ];
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
        return [['password', 'hash1:hash2:hash3'], ['password', 'hash1:hash2:hash3:hash4']];
    }

    /**
     * @param mixed $key
     *
     * @dataProvider encryptWithEmptyKeyDataProvider
     */
    public function testEncryptWithEmptyKey($key)
    {
        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue($key));
        $model = new Encryptor($this->_randomGenerator, $this->_cryptFactory, $deploymentConfigMock);
        $value = 'arbitrary_string';
        $this->assertEquals($value, $model->encrypt($value));
    }

    public function encryptWithEmptyKeyDataProvider()
    {
        return [[null], [0], [''], ['0']];
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider encryptDataProvider
     */
    public function testEncrypt($value, $expected)
    {
        $crypt = $this->getMock('Magento\Framework\Encryption\Crypt', [], [], '', false);
        $this->_cryptFactory->expects($this->once())->method('create')->will($this->returnValue($crypt));
        $crypt->expects($this->once())->method('encrypt')->with($value)->will($this->returnArgument(0));
        $actual = $this->_model->encrypt($value);
        $this->assertEquals($expected, $actual);
    }

    public function encryptDataProvider()
    {
        return [['value1', 'dmFsdWUx'], [true, 'MQ==']];
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider decryptDataProvider
     */
    public function testDecrypt($value, $expected)
    {
        $crypt = $this->getMock('Magento\Framework\Encryption\Crypt', [], [], '', false);
        $this->_cryptFactory->expects($this->once())->method('create')->will($this->returnValue($crypt));
        $crypt->expects($this->once())->method('decrypt')->with($expected)->will($this->returnValue($expected));
        $actual = $this->_model->decrypt($value);
        $this->assertEquals($expected, $actual);
    }

    public function decryptDataProvider()
    {
        return [['dmFsdWUx', 'value1']];
    }

    public function testValidateKey()
    {
        $crypt = $this->getMock('Magento\Framework\Encryption\Crypt', [], [], '', false);
        $this->_cryptFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            ['key' => 'some_key']
        )->will(
            $this->returnValue($crypt)
        );
        $this->assertSame($crypt, $this->_model->validateKey('some_key'));
    }

    public function testValidateKeyDefault()
    {
        $crypt = $this->getMock('Magento\Framework\Encryption\Crypt', [], [], '', false);
        $this->_cryptFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            ['key' => 'cryptKey']
        )->will(
            $this->returnValue($crypt)
        );
        $this->assertSame($crypt, $this->_model->validateKey(null));
        // Ensure crypt factory is invoked only once
        $this->assertSame($crypt, $this->_model->validateKey(null));
    }
}
