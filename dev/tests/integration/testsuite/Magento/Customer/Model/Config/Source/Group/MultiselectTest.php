<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source\Group;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Magento\Customer\Model\Config\Source\Group\Multiselect
 */
class MultiselectTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var Multiselect $multiselect */
        $multiselect = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Config\Source\Group\Multiselect');
        $this->assertEquals(
            array(
                array('value' => 1, 'label' => 'General'),
                array('value' => 2, 'label' => 'Wholesale'),
                array('value' => 3, 'label' => 'Retailer')
            ),
            $multiselect->toOptionArray()
        );
    }
}
