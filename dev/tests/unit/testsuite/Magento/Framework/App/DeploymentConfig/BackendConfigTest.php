<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class BackendConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetKey()
    {
        $object = new BackendConfig(['frontName' => 'backend']);
        $this->assertNotEmpty($object->getKey());
    }

    public function testGetData()
    {
        $object = new BackendConfig(['frontName' => 'backend']);
        $this->assertSame(['frontName' => 'backend'], $object->getData());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No backend frontname provided.
     */
    public function testEmptyData()
    {
        new BackendConfig([]);
    }

    /**
     * @param array $data
     * @dataProvider invalidDataDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid backend frontname
     */
    public function testInvalidData($data)
    {
        new BackendConfig($data);
    }

    /**
     * @return array
     */
    public function invalidDataDataProvider()
    {
        return [
            [['frontName' => '**']],
            [['frontName' => 'invalid frontname']],
        ];
    }
}
