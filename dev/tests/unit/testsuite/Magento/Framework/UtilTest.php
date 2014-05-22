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
        $this->assertEquals('5.5.7', $util->getTrimmedPhpVersion());
    }
}
