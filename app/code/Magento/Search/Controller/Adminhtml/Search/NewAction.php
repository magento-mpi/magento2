<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Search;

class NewAction extends \Magento\Search\Controller\Adminhtml\Search
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
