<?php
/**
 * Test \Magento\Customer\Service\V1\Data\Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\Data\Region;
use Magento\Customer\Service\V1\Data\RegionBuilder;

class RegionTest extends \PHPUnit_Framework_TestCase
{
    public function testRegion()
    {
        $region = new Region((new RegionBuilder())->setRegion('Alabama')->setRegionId(1)->setRegionCode('AL'));

        $this->assertEquals(1, $region->getRegionId());
        $this->assertEquals('AL', $region->getRegionCode());
        $this->assertEquals('Alabama', $region->getRegion());
    }
}
