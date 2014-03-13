<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Log\Model\Resource\Visitor\Online;

use Magento\TestFramework\Helper\Bootstrap;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Log/_files/visitor_online.php
     */
    public function testAddCustomerData()
    {
        /** @var \Magento\Log\Model\Resource\Visitor\Online\Collection $visitorOnlineCollection */
        $visitorOnlineCollection = Bootstrap::getObjectManager()
            ->create('Magento\Log\Model\Resource\Visitor\Online\Collection');

        $visitorOnlineCollection->addCustomerData();

        $this->assertEquals(1, $visitorOnlineCollection->count(), "Invalid collection items quantity.");
        /** @var \Magento\Log\Model\Visitor\Online $visitorOnline */
        $visitorOnline = $visitorOnlineCollection->getFirstItem();

        $expectedData = [
            'visitor_type' => 'c',
            'remote_addr' => '10101010',
            'first_visit_at' => '2014-03-02 00:00:00',
            'last_visit_at' => '2014-03-02 01:01:01',
            'customer_id' => 1,
            'last_url' => 'http://last_url',
            'customer_email' => 'customer@example.com',
            'customer_firstname' => 'Firstname',
            'customer_lastname' => 'Lastname'
        ];
        foreach ($expectedData as $field => $expectedValue) {
            $this->assertEquals(
                $expectedValue,
                $visitorOnline->getData($field),
                "'{$field}' field value is invalid."
            );
        }
    }
}
