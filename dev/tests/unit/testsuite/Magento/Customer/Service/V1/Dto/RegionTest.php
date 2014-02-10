<?php
/**
 * Test \Magento\Customer\Service\V1\Dto\Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Customer\Service\V1\Dto\Region;

class RegionTest extends \PHPUnit_Framework_TestCase
{
    public function testRegion()
    {
        $region = new Region([
            'region' => 'Alabama',
            'region_code' => 'AL',
            'region_id' => 1
        ]);

        $this->assertEquals(1, $region->getRegionId());
        $this->assertEquals('AL', $region->getRegionCode());
        $this->assertEquals('Alabama', $region->getRegion());
    }
}
