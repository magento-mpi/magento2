<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\Core\Model\StoreManagerInterface;
use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;
use Magento\Catalog\Model\Product;

class NewObject implements HandlerInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * String representation of the default product price
     *
     * @var string
     */
    protected $defaultProductPriceString;

    /**
     * @param StoreManagerInterface $storeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\PricePermissions\Helper\Data $pricePermData
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\App\RequestInterface $request,
        \Magento\PricePermissions\Helper\Data $pricePermData
    ) {
        $this->storeManager               = $storeManager;
        $this->request                    = $request;
        $this->defaultProductPriceString = $pricePermData->getDefaultProductPriceString();
    }

    /**
     * @param Product $product
     * @return void
     */
    public function handle(Product $product)
    {
        if (!$product->isObjectNew()) {
            return;
        }

        // For new products set default price
        if (!($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
            && $product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC)
        ) {
            $product->setPrice((float) $this->defaultProductPriceString);
            // Set default amount for Gift Card product
            if ($product->getTypeId() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD
            ) {
                $storeId = (int) $this->request->getParam('store', 0);
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                $product->setGiftcardAmounts(array(
                    array(
                        'website_id' => $websiteId,
                        'price'      => $this->defaultProductPriceString,
                        'delete'     => ''
                    )
                ));
            }
        }
        // Add MAP default values
        $product->setMsrpEnabled(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG
        );
        $product->setMsrpDisplayActualPriceType(
            \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG);

    }
} 
