<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Magento\Cms\Test\Block\Page;

/**
 * Class CmsPage
 * Cms Page for the frontend
 *
 * @package Magento\Cms\Test\Page
 */
class CmsPage extends \Mtf\Page\Page
{
    /**
     * Used only for page factory method creation
     */
    const MCA = 'cms/page';

    /**
     * Frontend Cms Page class
     *
     * @var string
     */
    protected $cmsPageClass = '.page .main';

    /**
     * Page initialization for building dynamically named Cms Page
     *
     * @param DataFixture|\Magento\Cms\Test\Fixture\Page $fixture
     */
    public function init(DataFixture $fixture)
    {
        $this->_url = $_ENV['app_frontend_url'] . $fixture->getPageIdentifier() . '/?___store=default';
    }

    /**
     * Get frontend Cms Page block
     *
     * @return Page
     */
    public function getCmsPageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCmsPage(
            $this->_browser->find($this->cmsPageClass, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Select last opened window
     *
     * @return Page
     */
    public function selectWindow()
    {
        $this->_browser->selectWindow();
    }
}
