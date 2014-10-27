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

use Magento\UrlRewrite\Model\UrlFinderInterface;

class Link extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param UrlFinderInterface $urlFinder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        UrlFinderInterface $urlFinder,
        array $data = array()
    ) {
        parent::__construct($context, $urlFinder, $data);
        $this->_entityResource = $catalogProduct;
    }
}
