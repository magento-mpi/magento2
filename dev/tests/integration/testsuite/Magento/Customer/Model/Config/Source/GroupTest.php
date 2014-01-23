<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Convert\Object as Converter;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testToOptionArray()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Group $group */
        $group = $objectManager->get(
            'Magento\Customer\Model\Config\Source\Group',
            array(
                'groupService' => $objectManager->get('Magento\Customer\Service\V1\CustomerGroupService'),
                'converter' => new Converter()
        ));
        $this->assertEquals(
            [
                ['value' => '', 'label' => '-- Please Select --'],
                ['value' => 1,  'label' => 'General'],
                ['value' => 2,  'label' => 'Wholesale'],
                ['value' => 3,  'label' => 'Retailer'],
                ['value' => 4,  'label' => 'custom_group']
            ],
            $group->toOptionArray());
    }
}
