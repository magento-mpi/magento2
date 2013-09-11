<?php
/**
 * Test for Magento_Filesystem_Stream_Zlib
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
        $stream = new Magento_Filesystem_Stream_Zlib(__DIR__ . DS . '..' . DS . '_files' . DS . 'popup.csv');
        $stream->open('rw+9f');
        $this->assertAttributeInstanceOf('Magento_Filesystem_Stream_Mode_Zlib', '_mode', $stream);
    }
}
