<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class SitemapNew
 *
 */
class SitemapNew extends BackendPage
{
    const MCA = 'sitemap/new/index';

    protected $_blocks = [
        'sitemapForm' => [
            'name' => 'sitemapForm',
            'class' => 'Magento\Backend\Test\Block\Widget\Form',
            'locator' => '#add_sitemap_form',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\Widget\Form
     */
    public function getSitemapForm()
    {
        return $this->getBlockInstance('sitemapForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }
}
