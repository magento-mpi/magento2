<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Plugin;

class CmsPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Plugin\CmsPage
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\VersionsCms\Model\Plugin\CmsPage();
    }

    public function testAfterGetAvailableStatuses()
    {
        $subjectMock = $this->getMock('Magento\Cms\Model\Page', array(), array(), '', false);
        $result = $this->_model->afterGetAvailableStatuses($subjectMock, array());
        $this->assertTrue(isset($result[\Magento\Cms\Model\Page::STATUS_ENABLED]));
        $this->assertEquals('Published', $result[\Magento\Cms\Model\Page::STATUS_ENABLED]);
    }
}
