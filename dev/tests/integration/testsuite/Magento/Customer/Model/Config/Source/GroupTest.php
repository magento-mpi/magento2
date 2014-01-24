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

/**
 * Class \Magento\Customer\Model\Config\Source\Group
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Group $group */
        $group = $objectManager->get(
            'Magento\Customer\Model\Config\Source\Group',
            [
                'groupService' => $objectManager->get('Magento\Customer\Service\V1\CustomerGroupService'),
                'converter' => new Converter()
            ]
        );
        $this->assertEquals(
            [
                ['value' => '', 'label' => '-- Please Select --'],
                ['value' => 1, 'label' => 'General'],
                ['value' => 2, 'label' => 'Wholesale'],
                ['value' => 3, 'label' => 'Retailer']
            ],
            $group->toOptionArray()
        );
    }
}
