<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Encryption;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Encryption\Model
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Encryption\Model');
    }

    public function testEncryptDecrypt()
    {
        $encryptor = $this->_model;

        $this->assertEquals('', $encryptor->decrypt($encryptor->encrypt('')));
        $this->assertEquals('test', $encryptor->decrypt($encryptor->encrypt('test')));
    }

    public function testEncryptDecrypt2()
    {
        $encryptor = $this->_model;

        $initial = md5(uniqid());
        $encrypted = $encryptor->encrypt($initial);
        $this->assertNotEquals($initial, $encrypted);
        $this->assertEquals($initial, $encryptor->decrypt($encrypted));
    }

    public function testValidateKey()
    {
        $validKey = md5(uniqid());
        $this->assertInstanceOf('Magento\Crypt', $this->_model->validateKey($validKey));
    }

    public function testGetValidateHash()
    {
        $password = uniqid();
        $hash = $this->_model->getHash($password);

        $this->assertTrue(is_string($hash));
        $this->assertTrue($this->_model->validateHash($password, $hash));
    }
}
