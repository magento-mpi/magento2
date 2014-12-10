<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pci\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Encryption\Crypt;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class EncryptionTest tests Magento\Pci\Model\Encryption
 */
class EncryptionTest extends \PHPUnit_Framework_TestCase
{
    public function testEncrypt()
    {
        // sample data to encrypt
        $data = 'Mares eat oats and does eat oats, but little lambs eat ivy.';
        $key = 'testKey';

        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryption::PARAM_CRYPT_KEY)
            ->will($this->returnValue($key));

        // Encrypt data with known key
        $objectManager = new ObjectManager($this);
        /**
         * @var \Magento\Pci\Model\Encryption
         */
        $encryption  = $objectManager->getObject(
            'Magento\Pci\Model\Encryption',
            ['deploymentConfig' => $deploymentConfigMock]
        );
        $actual = $encryption->encrypt($data);

        // Extract the initialization vector and encrypted data
        $parts = explode(':', $actual, 4);
        list(, , $iv, $encryptedData) = $parts;

        // Decrypt returned data with RIJNDAEL_256 cipher, cbc mode
        $crypt = new Crypt($key, MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, $iv);
        // Verify decrypted matches original data
        $this->assertEquals($data, $crypt->decrypt(base64_decode((string)$encryptedData)));
    }
}
