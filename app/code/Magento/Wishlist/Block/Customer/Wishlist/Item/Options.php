<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Block\Customer\Wishlist\Item;

/**
 * Wishlist block customer items
 */
class Options extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $_helperPool;

    /**
     * List of product options rendering configurations by product type
     *
     * @var array
     */
    protected $_optionsCfg = array(
        'default' => array(
            'helper' => 'Magento\Catalog\Helper\Product\Configuration',
            'template' => 'Magento_Wishlist::options_list.phtml'
        )
    );

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        array $data = array()
    ) {
        $this->_helperPool = $helperPool;
        parent::__construct(
            $context,
            $httpContext,
            $productFactory,
            $data
        );
    }

    /**
     * Initialize block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_eventManager->dispatch('product_option_renderer_init', array('block' => $this));
    }

    /**
     * Adds config for rendering product type options
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return $this
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
        $helper = $this->_helperPool->get($data['helper']);

        return $helper->getOptions($item);
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
