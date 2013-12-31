<?php
/**
 * Test \Magento\Customer\Service\Entity\V1\Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

use Magento\Customer\Service\Entity\V1\Region;

class RegionTest extends \PHPUnit_Framework_TestCase
{
    public function testRegion()
    {
        $region = new Region();
        $region->setRegion('Alabama')
            ->setRegionCode('AL')
            ->setRegionId(1);

        $this->assertEquals(1, $region->getRegionId());
        $this->assertEquals('AL', $region->getRegionCode());
        $this->assertEquals('Alabama', $region->getRegion());
    }

    public function testRegionCloneInnerObject()
    {
        $region2 = new Region();
        $region2->setRegion('Texas')
            ->setRegionCode('TX')
            ->setRegionId(33);

        $region = new Region();


        // here we use undocumented feature of Region to be able to accept entity of various types
        //as a value of the region. Just string is considered the right one.
        $region->setRegion($region2)
            ->setRegionCode('AL')
            ->setRegionId(1);

        $region1 = clone $region;

        $this->assertNotSame($region, $region1);
        $this->assertEquals(1, $region->getRegionId());
        $this->assertEquals('AL', $region->getRegionCode());
        $this->assertEquals($region2, $region->getRegion());

        $this->assertNotSame($region->getRegion(), $region1->getRegion());
        $this->assertEquals('AL', $region1->getRegionCode());

        $region2->setRegionId(66);
        $this->assertEquals(33, $region1->getRegion()->getRegionId());


        $region1->getRegion()->setRegionCode('MS');
        $this->assertEquals('TX', $region2->getRegionCode());

        $region1->setRegionCode('BL');
        $this->assertEquals('AL', $region->getRegionCode());
    }

    public function testRegionCloneInnerArray()
    {
        $region2 = new Region();
        $region2->setRegion('Texas')
            ->setRegionCode('TX')
            ->setRegionId(33);

        $region3 = new Region();
        $region3->setRegion('Massachusetts')
            ->setRegionCode('MA')
            ->setRegionId(22);

        $region = new Region();

        $region->setRegion(array($region2, $region3))
            ->setRegionCode('AL')
            ->setRegionId(1);

        $region1 = clone $region;
        $this->assertEquals(array($region2, $region3), $region->getRegion());
        $this->assertNotEquals(array($region2, $region3), $region1->getRegion());

        $this->assertEquals(33, $region2->getRegionId());
        $this->assertEquals(22, $region3->getRegionId());
    }
}
