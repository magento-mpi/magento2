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
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Stored price block instances
     * @var array
     */
    protected $_priceBlock = array();

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
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = array()
    ) {
        $this->_catalogData = $catalogData;
        parent::__construct($context, $httpContext, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return Price Block renderer for specified product type
     *
     * @param string $type Catalog Product type
     * @return \Magento\View\Element\Template
     */
    protected function _getPriceBlock($type)
    {
        if (!isset($this->_priceBlock[$type])) {
            /** @var \Magento\View\Element\RendererList $rendererList */
            $rendererList = $this->getRendererListName() ? $this->getLayout()->getBlock(
                $this->getRendererListName()
            ) : $this->getChildBlock(
                'renderer.list'
            );
            if (!$rendererList) {
                throw new \RuntimeException(
                    'Renderer list for block "' . $this->getNameInLayout() . '" is not defined'
                );
            }
            $overriddenTemplates = $this->getOverriddenTemplates() ?: array();
            $template = isset(
                $overriddenTemplates[$type]
            ) ? $overriddenTemplates[$type] : $this->getRendererTemplate();
            $renderer = $rendererList->getRenderer($type, self::DEFAULT_TYPE, $template);
            $this->_priceBlock[$type] = $renderer;
        }
        return $this->_priceBlock[$type];
    }

    /**
     * Returns product price html for RSS feed
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $displayMinimalPrice Display "As low as" etc.
     * @param string $idSuffix Suffix for HTML containers
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $typeId = $product->getTypeId();
        if ($this->_catalogData->canApplyMsrp($product)) {
            $typeId = $this->_mapRenderer;
        }

        return $this->_getPriceBlock(
            $typeId
        )->setProduct(
            $product
        )->setDisplayMinimalPrice(
            $displayMinimalPrice
        )->setIdSuffix(
            $idSuffix
        )->setUseLinkForAsLowAs(
            $this->_useLinkForAsLowAs
        )->toHtml();
    }
}
