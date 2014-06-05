<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class EditCmsPage
 * Page for edit Cms Page URL Rewrite
 */
class EditCmsPage extends BackendPage
{
    const MCA = 'admin/urlrewrite/edit/cms_page';

    protected $_blocks = [
        'gridBlock' => [
            'name' => 'gridBlock',
            'class' => 'Magento\Urlrewrite\Test\Block\Cms\Page\Grid',
            'locator' => '#cmsPageGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Urlrewrite\Test\Block\Cms\Page\Grid
     */
    public function getGridBlock()
    {
        return $this->getBlockInstance('gridBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
