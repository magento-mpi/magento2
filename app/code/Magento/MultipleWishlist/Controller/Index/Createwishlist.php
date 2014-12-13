<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\MultipleWishlist\Controller\IndexInterface;

class Createwishlist extends \Magento\Framework\App\Action\Action implements IndexInterface
{
    /**
     * Create new customer wishlist
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('editwishlist');
    }
}
