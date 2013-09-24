<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSendHeaders()
    {
        $response = new \Magento\TestFramework\Response(
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false)
        );
        $this->assertTrue($response->canSendHeaders());
        $this->assertTrue($response->canSendHeaders(false));
    }
}
