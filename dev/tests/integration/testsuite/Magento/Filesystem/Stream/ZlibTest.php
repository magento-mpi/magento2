<?php
/**
 * Test for \Magento\Filesystem\Stream\Zlib
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_ZlibTest extends PHPUnit_Framework_TestCase
{
    public function testOpen()
    {
        $stream = new \Magento\Filesystem\Stream\Zlib(__DIR__ . DS . '..' . DS . '_files' . DS . 'popup.csv');
        $stream->open('rw+9f');
        $this->assertAttributeInstanceOf('Magento\Filesystem\Stream\Mode\Zlib', '_mode', $stream);
    }
}
