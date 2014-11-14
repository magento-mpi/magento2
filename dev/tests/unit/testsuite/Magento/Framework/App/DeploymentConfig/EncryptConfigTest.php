<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;


class EncryptConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetKey()
    {
        $object = new EncryptConfig(['key' => str_pad('1', EncryptConfig::KEY_LENGTH, '1')]);
        $this->assertNotEmpty($object->getKey());
    }

    public function testGetData()
    {
        $key = str_pad('1', EncryptConfig::KEY_LENGTH, '1');
        $object = new EncryptConfig(['key' => $key]);
        $this->assertSame(['key' => $key], $object->getData());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No encryption key provided
     **/
    public function testEmptyData() {
        new EncryptConfig([]);
    }

    /**
     * @param array $data
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid encryption key:
     * @dataProvider invalidDataDataProvider
     */
    public function testInvalidData($data)
    {
        new EncryptConfig($data);
    }

    public function invalidDataDataProvider()
    {
        return [
            [['key' => 'a']],
            [['key' => str_pad('1', EncryptConfig::KEY_LENGTH + 1, '1')]],
            [['key' => str_pad('*', EncryptConfig::KEY_LENGTH, '*')]],
        ];
    }
}
