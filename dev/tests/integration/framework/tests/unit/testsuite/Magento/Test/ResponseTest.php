<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSendHeaders()
    {
        $response = new \Magento\TestFramework\Response(
            $this->getMock('Magento\Framework\Stdlib\CookieManager', [], [], '', false),
            $this->getMock('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory', [], [], '', false),
            $this->getMock('Magento\Framework\App\Http\Context', [], [], '', false)
        );
        $this->assertTrue($response->canSendHeaders());
        $this->assertTrue($response->canSendHeaders(false));
    }
}
