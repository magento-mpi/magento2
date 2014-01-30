<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block\Catalog;

class AbstractCatalog extends \Magento\Rss\Block\AbstractBlock
{
    /**
     * Stored price block instances
     * @var array
     */
    protected $_priceBlock = array();

    /**
     * Stored price blocks info
     * @var array
     */
    protected $_priceBlockTypes = array();

    /**
     * Default values for price block and template
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'rss/product/price.phtml';
    protected $_priceBlockDefaultType = 'Magento\Catalog\Block\Product\Price';

    /**
     * Whether to show "As low as" as a link
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = array()
    ) {
        $this->_catalogData = $catalogData;
        parent::__construct($context, $customerSession, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return Price Block renderer for specified product type
     *
     * @param string $productTypeId Catalog Product type
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_priceBlockDefaultType;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }
            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$productTypeId];
    }

    /**
     * Return template for Price Block renderer
     *
     * @param string $productTypeId Catalog Product type
     * @return string
     */
    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }

    /**
     * Returns product price html for RSS feed
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $displayMinimalPrice Display "As low as" etc.
     * @param string $idSuffix Suffix for HTML containers
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix='')
    {
        $typeId = $product->getTypeId();
        if ($this->_catalogData->canApplyMsrp($product)) {
            $typeId = $this->_mapRenderer;
        }

        return $this->_getPriceBlock($typeId)
            ->setTemplate($this->_getPriceBlockTemplate($typeId))
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs)
            ->toHtml();
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param string $type Catalog Product Type
     * @param string $block Block Type
     * @param string $template Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }
}
