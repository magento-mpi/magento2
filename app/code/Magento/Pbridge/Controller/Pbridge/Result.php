<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Result extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Result Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
