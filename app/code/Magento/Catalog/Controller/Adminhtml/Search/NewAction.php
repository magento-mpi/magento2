<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Search;

class NewAction extends \Magento\Catalog\Controller\Adminhtml\Search
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
