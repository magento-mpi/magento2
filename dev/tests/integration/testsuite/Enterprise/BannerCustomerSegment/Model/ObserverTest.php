<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_BannerCustomerSegment_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Banner/_files/banner.php
     * @magentoDataFixture Enterprise/CustomerSegment/_files/segment_developers.php
     * @magentoDataFixture Enterprise/CustomerSegment/_files/segment_designers.php
     */
    public function testSaveLoadCustomerSegmentRelations()
    {
        /** @var $segmentOne Enterprise_CustomerSegment_Model_Segment */
        $segmentOne = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
        $segmentOne->load('Developers', 'name');

        /** @var $segmentTwo Enterprise_CustomerSegment_Model_Segment */
        $segmentTwo = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
        $segmentTwo->load('Designers', 'name');

        $segmentIds = array($segmentOne->getId(), $segmentTwo->getId());

        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Test Banner', 'name');
        $banner->setData('customer_segment_ids', $segmentIds);
        $banner->save();

        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Test Banner', 'name');
        $this->assertEquals($segmentIds, $banner->getData('customer_segment_ids'));
    }

    /**
     * Test that only enabled banners not associated with customer groups are matched
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/BannerCustomerSegment/_files/banner_50_percent_off_on_ide.php
     * @magentoAppIsolation enabled
     */
    public function testAddCustomerSegmentFilterToCollectionCustomerWithoutSegments()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load(1);
        Mage::register('segment_customer', $customer);

        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        /** @var Enterprise_Banner_Model_Resource_Salesrule_Collection $collection */
        $collection = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Salesrule_Collection');
        $this->assertEquals(array($banner->getId()), $collection->getColumnValues('banner_id'));
    }

    /**
     * Test that only enabled banners, matching current customer's group or associated with no groups, are matched
     *
     * @magentoDataFixture Enterprise/CustomerSegment/_files/customer_developer.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/BannerCustomerSegment/_files/banner_50_percent_off_on_ide.php
     * @magentoDataFixture Enterprise/BannerCustomerSegment/_files/banner_40_percent_off_on_graphic_editor.php
     * @magentoAppIsolation enabled
     */
    public function testAddCustomerSegmentFilterToCollectionCustomerWithSegments()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load(1);
        Mage::register('segment_customer', $customer);

        /** @var Enterprise_Banner_Model_Banner $bannerForEverybody */
        $bannerForEverybody = Mage::getModel('Enterprise_Banner_Model_Banner');
        $bannerForEverybody->load('Get from 40% to 50% Off on Large Orders', 'name');

        /** @var Enterprise_Banner_Model_Banner $bannerForSegment */
        $bannerForSegment = Mage::getModel('Enterprise_Banner_Model_Banner');
        $bannerForSegment->load('Get 50% Off on Development IDEs', 'name');

        $expectedBannerIds = array($bannerForEverybody->getId(), $bannerForSegment->getId());

        /** @var Enterprise_Banner_Model_Resource_Salesrule_Collection $collection */
        $collection = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Salesrule_Collection');
        $this->assertEquals($expectedBannerIds, $collection->getColumnValues('banner_id'));
    }
}
