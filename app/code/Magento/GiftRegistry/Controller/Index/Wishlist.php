<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use \Magento\Framework\Model\Exception;

class Wishlist extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context, $coreRegistry, $formKeyValidator);
        $this->productRepository = $productRepository;
    }

    /**
     * Add wishlist items to customer active gift registry action
     *
     * @return void
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item');
        $redirectParams = array();
        if ($itemId) {
            try {
                $entity = $this->_initEntity('entity');
                $wishlistItem = $this->_objectManager->create(
                    'Magento\Wishlist\Model\Item'
                )->loadWithOptions(
                    $itemId,
                    'info_buyRequest'
                );
                $entity->addItem($wishlistItem->getProductId(), $wishlistItem->getBuyRequest());
                $this->messageManager->addSuccess(__('The wish list item has been added to this gift registry.'));
                $redirectParams['wishlist_id'] = $wishlistItem->getWishlistId();
            } catch (Exception $e) {
                if ($e->getCode() == \Magento\GiftRegistry\Model\Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $product = $this->productRepository->getById((int)$wishlistItem->getProductId());
                    $query['options'] = \Magento\GiftRegistry\Block\Product\View::FLAG;
                    $query['entity'] = $this->getRequest()->getParam('entity');
                    $this->getResponse()->setRedirect(
                        $product->getUrlModel()->getUrl($product, array('_query' => $query))
                    );
                    return;
                }
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('giftregistry');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn’t add your wish list items to your gift registry."));
            }
        }

        $this->_redirect('wishlist', $redirectParams);
    }
}
