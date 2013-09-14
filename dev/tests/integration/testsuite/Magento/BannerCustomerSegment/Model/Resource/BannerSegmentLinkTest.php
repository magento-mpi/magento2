<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_BannerCustomerSegment_Model_Resource_BannerSegmentLinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink
     */
    private $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = Mage::getResourceModel(
            'Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink'
        );
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
    }

    /**
     * @magentoDataFixture Magento/Banner/_files/banner.php
     * @magentoDataFixture Magento/CustomerSegment/_files/segment_developers.php
     * @magentoDataFixture Magento/BannerCustomerSegment/_files/banner_40_percent_off_on_graphic_editor.php
     * @dataProvider saveLoadBannerSegmentsDataProvider
     * @param string $bannerName
     * @param mixed $segmentNames
     */
    public function testSaveLoadBannerSegments($bannerName, $segmentNames)
    {
        $bannerId = $this->_getBannerId($bannerName);
        $segmentIds = $segmentNames ? $this->_getSegmentIds($segmentNames) : array();

        $this->_resourceModel->saveBannerSegments($bannerId, $segmentIds);

        $actualSegmentIds = $this->_resourceModel->loadBannerSegments($bannerId);
        $this->assertEquals($segmentIds, $actualSegmentIds, '', 0, 10, true); // ignore order
    }

    public function saveLoadBannerSegmentsDataProvider()
    {
        $bannerForSegment = 'Get 40% Off on Graphic Editors';
        return array(
            'initial add single'        => array('Test Banner', array('Designers')),
            'initial add multiple'      => array('Test Banner', array('Developers', 'Designers')),
            'override all'              => array($bannerForSegment, array('Developers')),
            'add missing'               => array($bannerForSegment, array('Designers', 'Developers')),
            'remove all - empty array'  => array($bannerForSegment, array()),
            'remove all - empty string' => array($bannerForSegment, ''),
            'remove all - null'         => array($bannerForSegment, null),
        );
    }

    /**
     * @magentoDataFixture Magento/Banner/_files/banner_disabled_40_percent_off.php
     * @magentoDataFixture Magento/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Magento/BannerCustomerSegment/_files/banner_50_percent_off_on_ide.php
     * @magentoDataFixture Magento/BannerCustomerSegment/_files/banner_40_percent_off_on_graphic_editor.php
     * @dataProvider addBannerSegmentFilterDataProvider
     * @param array $segmentNames
     * @param array $expectedBannerNames
     */
    public function testAddBannerSegmentFilter(array $segmentNames, array $expectedBannerNames)
    {
        $expectedBannerIds = array();
        foreach ($expectedBannerNames as $bannerName) {
            $expectedBannerIds[] = $this->_getBannerId($bannerName);
        }

        /** @var \Magento\Banner\Model\Resource\Salesrule\Collection $collection */
        $collection = Mage::getResourceModel('Magento\Banner\Model\Resource\Salesrule\Collection');
        $select = $collection->getSelect();
        $initialSql = (string)$select;

        $this->_resourceModel->addBannerSegmentFilter($select, $this->_getSegmentIds($segmentNames));

        $this->assertNotEquals($initialSql, (string)$select, 'Query is expected to be modified.');
        $actualBannerIds = $select->getAdapter()->fetchCol($select);
        $this->assertEquals($expectedBannerIds, $actualBannerIds, '', 0, 10, true); // ignore order
    }

    public function addBannerSegmentFilterDataProvider()
    {
        return array(
            'only banners for everybody'  => array(
                array(),
                array('Get from 40% to 50% Off on Large Orders'),
            ),
            'banners for everybody + for specific segment' => array(
                array('Developers'),
                array(
                    'Get from 40% to 50% Off on Large Orders',
                    'Get 50% Off on Development IDEs',
                )
            ),
            'banners for everybody + for specific segments' => array(
                array('Developers', 'Designers'),
                array(
                    'Get from 40% to 50% Off on Large Orders',
                    'Get 50% Off on Development IDEs',
                    'Get 40% Off on Graphic Editors',
                )
            ),
        );
    }

    /**
     * Retrieve banner ID by its name
     *
     * @param string $bannerName
     * @return int|null
     */
    protected function _getBannerId($bannerName)
    {
        /** @var \Magento\Banner\Model\Banner $banner */
        $banner = Mage::getModel('Magento\Banner\Model\Banner');
        $banner->load($bannerName, 'name');
        return $banner->getId();
    }

    /**
     * Retrieve segment IDs by names
     *
     * @param array $segmentNames
     * @return array
     */
    protected function _getSegmentIds(array $segmentNames)
    {
        $result = array();
        foreach ($segmentNames as $segmentName) {
            /** @var $segment \Magento\CustomerSegment\Model\Segment */
            $segment = Mage::getModel('Magento\CustomerSegment\Model\Segment');
            $segment->load($segmentName, 'name');
            $result[] = $segment->getId();
        }
        return $result;
    }
}
