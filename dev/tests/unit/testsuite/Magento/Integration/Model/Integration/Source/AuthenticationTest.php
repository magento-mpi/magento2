<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration\Source;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var \Magento\Integration\Model\Integration\Source\Authentication */
        $authSource = new \Magento\Integration\Model\Integration\Source\Authentication();
        /** @var array */
        $expectedAuthArr = array(
            \Magento\Integration\Model\Integration::AUTHENTICATION_OAUTH => __('OAuth'),
            \Magento\Integration\Model\Integration::AUTHENTICATION_MANUAL => __('Manual'),
        );
        $authArr = $authSource->toOptionArray();
        $this->assertEquals($expectedAuthArr, $authArr, "Authentication source arrays don't match");
    }
}
