<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Onepagesuccess extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Redirect to Onepage checkout success page
     *
     * @return void
     */
    public function execute()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
