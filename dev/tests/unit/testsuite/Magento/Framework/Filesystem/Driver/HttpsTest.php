<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Driver;

class HttpsTest extends \PHPUnit_Framework_TestCase
{
    /** @var bool Result of fsockopen() function */
    public static $fSockOpen;

    public function setUp()
    {
        require_once __DIR__ . '/../_files/http_mock.php';
        self::$fSockOpen = true;
    }

    public function testFileOpen()
    {
        $fSockOpenResult = 'resource';
        self::$fSockOpen = $fSockOpenResult;
        $this->assertEquals($fSockOpenResult, (new Https())->fileOpen('example.com', 'r'));
    }
}
