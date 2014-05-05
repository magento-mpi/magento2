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
 * Class SitemapEdit
 *
 * @package Magento\Sitemap\Test\Page\Adminhtml
 */
class SitemapEdit extends BackendPage
{
    const MCA = 'admin/sitemap/edit';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }
}
