<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display link to the product
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Product\Widget;

class Link
    extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($urlRewrite, $storeManager, $coreData, $context, $data);
        $this->_entityResource = $catalogProduct;
    }
}
