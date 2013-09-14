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
namespace Magento\Wishlist\Block\Customer\Wishlist\Item;

class Options extends \Magento\Wishlist\Block\AbstractBlock
{
    /*
     * List of product options rendering configurations by product type
     *
     * @var array
     */
    protected $_optionsCfg = array('default' => array(
        'helper' => 'Magento\Catalog\Helper\Product\Configuration',
        'template' => 'Magento_Wishlist::options_list.phtml'
    ));

    /**
     * Initialize block
     */
    protected function _construct()
    {
        parent::_construct();
        \Mage::dispatchEvent('product_option_renderer_init', array('block' => $this));
    }

    /*
     * Adds config for rendering product type options
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return \Magento\Wishlist\Block\Customer\Wishlist\Item\Options
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    /**
     * Get item options renderer config
     *
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
     * Retrieve product configured options
     *
     * @return array
     */
    public function getConfiguredOptions()
    {
        $item = $this->getItem();
        $data = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (empty($data['helper'])
            || !$this->helper($data['helper']) instanceof \Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface
        ) {
            \Mage::throwException(__("Helper for wish list options rendering doesn't implement required interface."));
        }

        return $this->helper($data['helper'])->getOptions($item);
    }

    /**
     * Retrieve block template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = parent::getTemplate();
        if ($template) {
            return $template;
        }

        $item = $this->getItem();
        if (!$item) {
            return '';
        }
        $data = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (empty($data['template'])) {
            $data = $this->getOptionsRenderCfg('default');
        }

        return empty($data['template']) ? '' : $data['template'];
    }

    /**
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setOptionList($this->getConfiguredOptions());

        return parent::_toHtml();
    }
}
