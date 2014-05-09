<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Simplexml;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testXmlentities()
    {
        $xmlElement = new \Magento\Shipping\Model\Simplexml\Element('<xml></xml>');
        $this->assertEquals('&amp;copy;&amp;', $xmlElement->xmlentities('&copy;&amp;'));
    }
}
