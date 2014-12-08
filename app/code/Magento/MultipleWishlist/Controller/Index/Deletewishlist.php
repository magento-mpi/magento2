<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\MultipleWishlist\Controller\IndexInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Deletewishlist extends \Magento\Framework\App\Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(Context $context, WishlistProviderInterface $wishlistProvider)
    {
        $this->wishlistProvider = $wishlistProvider;
        parent::__construct($context);
    }

    /**
     * Delete wishlist by id
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     * @throws NotFoundException
     */
    public function execute()
    {
        try {
            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException();
            }
            if ($this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isWishlistDefault($wishlist)) {
                throw new \Magento\Framework\Model\Exception(__('The default wish list cannot be deleted.'));
            }
            $wishlist->delete();
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $this->messageManager->addSuccess(
                __(
                    'Wish list "%1" has been deleted.',
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                )
            );
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong deleting the wish list.');
            $this->messageManager->addException($e, $message);
        }
    }
}
