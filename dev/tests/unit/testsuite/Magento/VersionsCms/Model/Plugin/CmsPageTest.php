<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Plugin_CmsPageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_VersionsCms_Model_Plugin_CmsPage
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_VersionsCms_Model_Plugin_CmsPage();
    }

    public function testAfterGetAvailableStatuses()
    {
        $result = $this->_model->afterGetAvailableStatuses(array());
        $this->assertTrue(isset($result[Magento_Cms_Model_Page::STATUS_ENABLED]));
        $this->assertEquals('Published', $result[Magento_Cms_Model_Page::STATUS_ENABLED]);
    }
}
