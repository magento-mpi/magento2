<?php
/**
 * Created by PhpStorm.
 * User: bimathew
 * Date: 7/22/14
 * Time: 10:44 AM
 */
namespace Magento\Framework\Stdlib\Cookie;

use Magento\TestFramework\Helper\ObjectManager;

class SensitiveCookieMetadataTest extends \PHPUnit_Framework_TestCase
{
    /** @var  SensitiveCookieMetadata */
    private $sensitiveCookieMetadata;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->sensitiveCookieMetadata = $objectManager->getObject(
            'Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata'
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
        $this->sensitiveCookieMetadata->$setMethodName($expectedValue);
        $this->assertSame($expectedValue, $this->sensitiveCookieMetadata->$getMethodName());
    }

    /**
     * @return array
     */
    public function getMethodData()
    {
        return [
            "getDomain" => ["setDomain", 'getDomain', "example.com"],
            "getPath" => ["setPath", 'getPath', "path"]
        ];
    }
}
