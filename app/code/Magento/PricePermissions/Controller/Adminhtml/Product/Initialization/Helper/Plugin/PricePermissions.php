<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class PricePermissions 
{
    /**
     * Backend authorization session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Helper data
     *
     * @var \Magento\PricePermissions\Helper\Data
     */
    protected $pricePermData;

    /**
     * Handler interface
     *
     * @var HandlerInterface
     */
    protected $productHandler;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\PricePermissions\Helper\Data $pricePermData
     * @param HandlerInterface $productHandler
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\PricePermissions\Helper\Data $pricePermData,
        HandlerInterface $productHandler
    ) {
        $this->pricePermData = $pricePermData;
        $this->authSession = $authSession;
        $this->productHandler = $productHandler;
    }

    /**
     * Handle important product data before saving a product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterInitialize(\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject, \Magento\Catalog\Model\Product $product)
    {
        $canEditProductPrice = false;
        if ($this->authSession->isLoggedIn() && $this->authSession->getUser()->getRole()) {
            $canEditProductPrice = $this->pricePermData->getCanAdminEditProductPrice();
        }

        if ($canEditProductPrice) {
            return $product;
        }

        $this->productHandler->handle($product);

        return $product;
    }
} 
