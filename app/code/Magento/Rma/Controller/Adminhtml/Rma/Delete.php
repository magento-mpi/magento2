<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Delete extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Delete rma
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect('adminhtml/*/');
    }
}
