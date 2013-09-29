<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Model\Resource\Rule;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Reminder/_files/rules.php
     */
    public function testAddDateFilter()
    {
        $dateModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Date');
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Reminder\Model\Resource\Rule\Collection');
        $collection->addDateFilter($dateModel->date());
        $this->markTestIncomplete('MAGE-5166 is incomplete');
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $rule) {
            $this->assertInstanceOf('Magento\Reminder\Model\Rule', $rule);
            $this->assertEquals('Rule 2', $rule->getName());
            return;
        }
        $this->fail('Collection has not been loaded properly.');
    }
}
