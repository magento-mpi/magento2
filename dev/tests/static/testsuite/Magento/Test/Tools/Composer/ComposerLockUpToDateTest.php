<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer;

/**
 * Class ComposerLockUpToDateTest
 */
class ComposerLockUpToDateTest extends \PHPUnit_Framework_TestCase
{
    public function testUpToDate()
    {
        $hash = hash_file('md5', __DIR__ . '/../../../../../../../../composer.json');
        $jsonData = file_get_contents(__DIR__ . '/../../../../../../../../composer.lock');
        $json = json_decode($jsonData);
        $this->assertTrue($hash === $json->hash, 'composer.lock file not up to date');
    }
}
