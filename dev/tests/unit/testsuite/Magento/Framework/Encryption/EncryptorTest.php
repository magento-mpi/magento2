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

    protected function setUp()
    {
        $this->_randomGenerator = $this->getMock('Magento\Framework\Math\Random', array(), array(), '', false);
        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', array(), array(), '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue('cryptKey'));
        $this->_model = new \Magento\Framework\Encryption\Encryptor($this->_randomGenerator, $deploymentConfigMock);
    }

    public function testGetHashNoSalt()
    {
        $this->_randomGenerator->expects($this->never())->method('getRandomString');
        $expected = '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8';
        $actual = $this->_model->getHash('password');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashSpecifiedSalt()
    {
        $this->_randomGenerator->expects($this->never())->method('getRandomString');
        $expected = '13601bda4ea78e55a07b98866d2be6be0744e3866f13c00c811cab608a28f322:salt';
        $actual = $this->_model->getHash('password', 'salt');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashRandomSaltDefaultLength()
    {
        $this->_randomGenerator
            ->expects($this->once())
            ->method('getRandomString')
            ->with(32)
            ->will($this->returnValue('-----------random_salt----------'));
        $expected = 'a1c7fc88037b70c9be84d3ad12522c7888f647915db78f42eb572008422ba2fa:-----------random_salt----------';
        $actual = $this->_model->getHash('password', true);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHashRandomSaltSpecifiedLength()
    {
        $this->_randomGenerator
            ->expects($this->once())
            ->method('getRandomString')
            ->with(11)
            ->will($this->returnValue('random_salt'));
        $expected = '4c5cab8dd00137d11258f8f87b93fd17bd94c5026fc52d3c5af911dd177a2611:random_salt';
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
            array('password', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', true),
            array('password', '67a1e09bb1f83f5007dc119c14d663aa:salt', true)
        );
    }

    /**
     * @param mixed $key
     *
     * @dataProvider encryptWithEmptyKeyDataProvider
     */
    public function testEncryptWithEmptyKey($key)
    {
        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', array(), array(), '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryptor::PARAM_CRYPT_KEY)
            ->will($this->returnValue($key));
        $model = new Encryptor($this->_randomGenerator, $deploymentConfigMock);
        $value = 'arbitrary_string';
        $this->assertEquals($value, $model->encrypt($value));
    }

    public function encryptWithEmptyKeyDataProvider()
    {
        return array(array(null), array(0), array(''), array('0'));
    }

    public function testEncrypt()
    {
        // sample data to encrypt
        $data = 'Mares eat oats and does eat oats, but little lambs eat ivy.';

        $actual = $this->_model->encrypt($data);

        // Extract the initialization vector and encrypted data
        $parts = explode(':', $actual, 4);
        list(, , $iv, $encryptedData) = $parts;
        echo $actual;

        // Decrypt returned data with RIJNDAEL_256 cipher, cbc mode
        $crypt = new Crypt('cryptKey', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $iv);
        // Verify decrypted matches original data
        $this->assertEquals($data, $crypt->decrypt(base64_decode((string)$encryptedData)));
    }

    public function testDecrypt()
    {
        // sample data to encrypt
        $data = '0:2:z3a4ACpkU35W6pV692U4ueCVQP0m0v0p:' .
            '7ZPIIRZzQrgQH+csfF3fyxYNwbzPTwegncnoTxvI3OZyqKGYlOCTSx5i1KRqNemCC8kuCiOAttLpAymXhzjhNQ==';

        $actual = $this->_model->decrypt($data);

        // Extract the initialization vector and encrypted data
        $parts = explode(':', $data, 4);
        list(, , $iv, $encrypted) = $parts;

        // Decrypt returned data with RIJNDAEL_256 cipher, cbc mode
        $crypt = new Crypt('cryptKey', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $iv);
        // Verify decrypted matches original data
        $this->assertEquals($encrypted, base64_encode($crypt->encrypt($actual)));
    }

    public function testValidateKey()
    {
        $actual = $this->_model->validateKey('some_key');
        $crypt = new Crypt('some_key', MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $actual->getInitVector());
        $expectedEncryptedData = base64_encode($crypt->encrypt('data'));
        $actualEncryptedData = base64_encode($actual->encrypt('data'));
        $this->assertEquals($expectedEncryptedData, $actualEncryptedData);
        $this->assertEquals($crypt->decrypt($expectedEncryptedData), $actual->decrypt($actualEncryptedData));
    }
}
