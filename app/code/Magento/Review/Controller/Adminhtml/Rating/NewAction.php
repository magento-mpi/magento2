<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml\Rating;

class NewAction extends \Magento\Review\Controller\Adminhtml\Rating
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
