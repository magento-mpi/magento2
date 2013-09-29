<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist block customer items
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Block\Customer;

class Wishlist extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $_helperPool;

    /**
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_helperPool = $helperPool;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Add wishlist conditions to collection
     *
     * @param  \Magento\Wishlist\Model\Resource\Item\Collection $collection
     * @return \Magento\Wishlist\Block\Customer\Wishlist
     */
    protected function _prepareCollection($collection)
    {
        $collection->setInStockFilter(true)->setOrder('added_at', 'ASC');
        return $this;
    }

    /**
     * Preparing global layout
     *
     * @return \Magento\Wishlist\Block\Customer\Wishlist
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('My Wish List'));
        }
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Sets all options render configurations
     *
     * @deprecated after 1.6.2.0
     * @param null|array $optionCfg
     * @return \Magento\Wishlist\Block\Customer\Wishlist
     */
    public function setOptionsRenderCfgs($optionCfg)
    {
        $this->_optionsCfg = $optionCfg;
        return $this;
    }

    /**
     * Returns all options render configurations
     *
     * @deprecated after 1.6.2.0
     * @return array
     */
    public function getOptionsRenderCfgs()
    {
        return $this->_optionsCfg;
    }

    /*
     * Adds config for rendering product type options
     *
     * @deprecated after 1.6.2.0
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return \Magento\Wishlist\Block\Customer\Wishlist
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    /**
     * Returns html for showing item options
     *
     * @deprecated after 1.6.2.0
     * @param string $productType
     * @return array|null
     */
    public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    /**
     * Returns html for showing item options
     *
     * @deprecated after 1.6.2.0
     * @param \Magento\Wishlist\Model\Item $item
     * @return string
     */
    public function getDetailsHtml(\Magento\Wishlist\Model\Item $item)
    {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $block  = $this->getChildBlock('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }
            $template = $cfgDefault['template'];
        }

        return $block->setTemplate($template)
            ->setOptionList($this->_helperPool($cfg['helper'])->getOptions($item))
            ->toHtml();
    }

    /**
     * Returns qty to show visually to user
     *
     * @deprecated after 1.6.2.0
     * @param \Magento\Wishlist\Model\Item $item
     * @return float
     */
    public function getAddToCartQty(\Magento\Wishlist\Model\Item $item)
    {
        $qty = $this->getQty($item);
        return $qty ? $qty : 1;
    }
}
