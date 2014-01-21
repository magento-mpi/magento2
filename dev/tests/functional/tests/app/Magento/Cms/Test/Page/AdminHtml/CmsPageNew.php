<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Test\Page\AdminHtml;

use Magento\Cms\Test\Block\AdminHtml\Page\Edit;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for creating new cms page in backend
 *
 * @package Magento\Cms\Test\Page\AdminHtml
 */
class CmsPageNew extends Page
{
    /**
     * URL for cms page
     */
    const MCA = 'admin/cms_page/new';

    /**
     * Form for creation of the cms page
     *
     * @var string
     */
    protected $cmsPageForm = 'page:main-container';

    /**
     * Initialize page. Set page url
     *
     * @return void
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get new cms page form
     *
     * @return Edit
     */
    public function getNewCmsPageForm()
    {
        return Factory::getBlockFactory()->getMagentoCmsAdminHtmlPageEdit(
            $this->_browser->find($this->cmsPageForm, Locator::SELECTOR_ID)
        );
    }
}
