<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Framework\App\DeploymentConfig;

/**
 * Class EncryptionTest tests Magento\Pbridge\Model\Encryption
 */
class EncryptionTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryptPB()
    {
        // sample data to encrypt
        $data = 'Mares eat oats and does eat oats, but little lambs eat ivy.';

        // Encrypt data with known key
        $objectManager = new ObjectManager($this);

        $deploymentConfigMock = $this->getMock('\Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $deploymentConfigMock->expects($this->any())
            ->method('get')
            ->with(Encryption::PARAM_CRYPT_KEY)
            ->will($this->returnValue('testKey'));

        /**
         * @var \Magento\Pbridge\Model\Encryption
         */
        $encryption  = $objectManager->getObject(
            'Magento\Pbridge\Model\Encryption',
            ['deploymentConfg' => $deploymentConfigMock, 'key' => 'qwerty']
        );
        $actual = $encryption->encrypt($data);
        // Extract the initialization vector and encrypted data
        $parts = explode(':', $actual);
        $actualIvSize = $parts[0];

        // Emulate PB initialization vector size
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $ivSize = mcrypt_enc_get_iv_size($module);

        $this->assertEquals($ivSize, strlen($actualIvSize));
    }
}
