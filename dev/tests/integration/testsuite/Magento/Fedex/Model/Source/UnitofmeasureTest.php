<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Fedex\Model\Source;

class UnitofmeasureTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var $model \Magento\Fedex\Model\Source\Unitofmeasure */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Fedex\Model\Source\Unitofmeasure'
        );
        $result = $model->toOptionArray();
        $this->assertCount(2, $result);
    }
}
