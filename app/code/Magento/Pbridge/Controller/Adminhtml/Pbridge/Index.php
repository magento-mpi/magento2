<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Adminhtml\Pbridge;

class Index extends \Magento\Pbridge\Controller\Adminhtml\Pbridge
{
    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('result');
    }
}
