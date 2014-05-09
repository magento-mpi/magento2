<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display link to the product
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\Widget;

class Link extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\UrlRewrite\Model\Resource\UrlRewrite $urlRewrite
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\UrlRewrite\Model\Resource\UrlRewrite $urlRewrite,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        array $data = array()
    ) {
        parent::__construct($context, $urlRewrite, $data);
        $this->_entityResource = $catalogProduct;
    }
}
