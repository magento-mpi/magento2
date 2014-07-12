<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;
use Magento\Cms\Test\Block\Page;

/**
 * Class CmsPage
 * Cms Page for the frontend
 *
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
    protected $cmsPageClass = '.page.main';

    /**
     * Page initialization for building dynamically named Cms Page
     *
     * @param FixtureInterface|\Magento\Cms\Test\Fixture\CmsPage $fixture
     * @return void
     */
    public function init(FixtureInterface $fixture)
    {
        $this->_url = $_ENV['app_frontend_url'] . $fixture->getIdentifier() . '/?___store=default';
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

    /**
     * Check is visible widget selector
     *
     * @param $widgetSelector
     * @return bool
     */
    public function widgetSelectorIsVisible($widgetSelector)
    {
        return $this->_browser->find($widgetSelector)->isVisible();
    }
}
