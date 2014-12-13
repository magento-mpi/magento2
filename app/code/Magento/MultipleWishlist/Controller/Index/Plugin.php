<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

class Plugin
{
    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\MultipleWishlist\Helper\Data $helper
     */
    public function __construct(\Magento\MultipleWishlist\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Check whether multiple wishlist feature is enabled
     *
     * @param \Magento\MultipleWishlist\Controller\IndexInterface $subject
     * @param RequestInterface $request
     * @return void
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function beforeDispatch(
        \Magento\MultipleWishlist\Controller\IndexInterface $subject,
        RequestInterface $request
    ) {
        if (!$this->helper->isMultipleEnabled()) {
            throw new NotFoundException();
        }
    }
}
