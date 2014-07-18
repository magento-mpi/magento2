<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
