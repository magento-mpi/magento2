<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller\Cart;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * No index action, forward to 404
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('noroute');
    }
}
