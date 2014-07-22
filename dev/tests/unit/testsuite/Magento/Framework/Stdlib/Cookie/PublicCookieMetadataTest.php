<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Test PublicCookieMetadata
 *
 */
class PublicCookieMetadataTest extends \PHPUnit_Framework_TestCase
{
    /** @var PublicCookieMetadata */
    private $publicCookieMetadata;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->publicCookieMetadata = $objectManager->getObject(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata'
        );
    }

    /**
     * @param String $setMethodName
     * @param String $getMethodName
     * @param String $expectedValue
     * @dataProvider getMethodData
     */

    public function testGetters($setMethodName, $getMethodName, $expectedValue)
    {
        $this->publicCookieMetadata->$setMethodName($expectedValue);
        $this->assertSame($expectedValue, $this->publicCookieMetadata->$getMethodName());
    }

    /**
     * @return array
     */
    public function getMethodData()
    {
        return [
            "getDomain" => ["setDomain", 'getDomain', "example.com"],
            "getPath" => ["setPath", 'getPath', "path"],
            "getDuration" => ["setDuration", 'getDuration', 125],
            "getHttpOnly" => ["setHttpOnly", 'getHttpOnly', true],
            "getSecure" => ["setSecure", 'getSecure', true]
        ];
    }
}
