<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page; 

use Mtf\Page\FrontendPage; 

/**
 * Class CmsIndex
 *
 * @package Magento\Cms\Test\Page
 */
class CmsIndex extends FrontendPage
{
    const MCA = 'cms/index/index';

    protected $_blocks = [
        'searchBlock' => [
            'name' => 'searchBlock',
            'class' => 'Magento\Catalog\Test\Block\Search',
            'locator' => '#search_mini_form',
            'strategy' => 'css selector',
        ],
        'topmenuBlock' => [
            'name' => 'topmenuBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Topmenu',
            'locator' => '[role=navigation]',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page.title',
            'strategy' => 'css selector',
        ],
        'footerBlock' => [
            'name' => 'footerBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Footer',
            'locator' => 'footer.footer',
            'strategy' => 'css selector',
        ],
        'linksBlock' => [
            'name' => 'linksBlock',
            'class' => 'Magento\Theme\Test\Block\Links',
            'locator' => '.header .links',
            'strategy' => 'css selector',
        ],
        'storeSwitcherBlock' => [
            'name' => 'storeSwitcherBlock',
            'class' => 'Magento\Store\Test\Block\Switcher',
            'locator' => '//*[@data-ui-id="language-switcher"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Search
     */
    public function getSearchBlock()
    {
        return $this->getBlockInstance('searchBlock');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Topmenu
     */
    public function getTopmenuBlock()
    {
        return $this->getBlockInstance('topmenuBlock');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Footer
     */
    public function getFooterBlock()
    {
        return $this->getBlockInstance('footerBlock');
    }

    /**
     * @return \Magento\Theme\Test\Block\Links
     */
    public function getLinksBlock()
    {
        return $this->getBlockInstance('linksBlock');
    }

    /**
     * @return \Magento\Store\Test\Block\Switcher
     */
    public function getStoreSwitcherBlock()
    {
        return $this->getBlockInstance('storeSwitcherBlock');
    }
}
