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
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $urlRewrite, $data);
        $this->_entityResource = $catalogProduct;
    }
}
