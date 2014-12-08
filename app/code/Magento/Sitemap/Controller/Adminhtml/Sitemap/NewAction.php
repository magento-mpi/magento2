<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Controller\Adminhtml\Sitemap;


class NewAction extends \Magento\Sitemap\Controller\Adminhtml\Sitemap
{
    /**
     * Create new sitemap
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
