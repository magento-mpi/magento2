<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Success extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Review success action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
