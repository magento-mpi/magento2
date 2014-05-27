<?php
/**
 * Collection of various useful functions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTrimmedPhpVersion()
    {
        $util = new \Magento\Framework\Util();
        $version = implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]);
        $this->assertEquals($version, $util->getTrimmedPhpVersion());
    }
}
