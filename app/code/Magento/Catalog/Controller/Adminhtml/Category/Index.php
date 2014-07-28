<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Index extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Catalog categories index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
