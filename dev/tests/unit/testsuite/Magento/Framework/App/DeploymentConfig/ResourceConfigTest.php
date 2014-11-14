<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;


class ResourceConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetKey()
    {
        $object = new ResourceConfig([]);
        $this->assertNotEmpty($object->getKey());
    }

    public function testGetData()
    {
        $data = [
            'test' => [
                'name' => 'test',
                ResourceConfig::KEY_CONNECTION => 'default',
            ]
        ];
        $expected = [
            'default_setup' => [
                'name' => 'default_setup',
                ResourceConfig::KEY_CONNECTION => 'default',
            ],
            'test' => $data['test'],
        ];

        $object = new ResourceConfig($data);
        $this->assertSame($expected, $object->getData());
    }

    public function testEmptyData()
    {
        $data = [
            'default_setup' => [
                'name' => 'default_setup',
                ResourceConfig::KEY_CONNECTION => 'default',
            ]
        ];
        $object = new ResourceConfig([]);
        $this->assertSame($data, $object->getData());
    }

    /**
     * @param array $data
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid resource configuration.
     * @dataProvider invalidDataDataProvider
     */
    public function testInvalidData($data)
    {
        new ResourceConfig($data);
    }

    public function invalidDataDataProvider()
    {
        return [
            [
                [
                    'no_name' => [
                    ResourceConfig::KEY_CONNECTION => 'default',]
                ],
                [
                    'no_connection' => [
                        'name' => 'no_connection',]
                ],
                [
                    'wrong_name' => [
                        'name' => 'other_name',
                        ResourceConfig::KEY_CONNECTION => 'default',]
                ],
            ],
        ];
    }
}
