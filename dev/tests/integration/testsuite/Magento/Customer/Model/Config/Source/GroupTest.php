<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Magento\Customer\Model\Config\Source\Group
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var Group $group */
        $group = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Config\Source\Group');
        $this->assertEquals(
            array(
                array('value' => '', 'label' => '-- Please Select --'),
                array('value' => 1, 'label' => 'General'),
                array('value' => 2, 'label' => 'Wholesale'),
                array('value' => 3, 'label' => 'Retailer')
            ),
            $group->toOptionArray()
        );
    }
}
