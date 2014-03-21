<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Integration\Source;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var \Magento\Integration\Model\Integration\Source\Status */
        $statusSource = new \Magento\Integration\Model\Integration\Source\Status();
        /** @var array */
        $expectedStatusArr = array(
            array('value' => \Magento\Integration\Model\Integration::STATUS_INACTIVE, 'label' => __('Inactive')),
            array('value' => \Magento\Integration\Model\Integration::STATUS_ACTIVE, 'label' => __('Active'))
        );
        $statusArr = $statusSource->toOptionArray();
        $this->assertEquals($expectedStatusArr, $statusArr, "Status source arrays don't match");
    }
}
