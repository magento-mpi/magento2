<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers wishlist grid item renderer for name/options cell
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Catalog product configuration
     *
     * @var Magento_Catalog_Helper_Product_Configuration
     */
    protected $_catalogProductConfiguration = null;

    /**
     * @param Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Configuration $catalogProductConfiguration,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_catalogProductConfiguration = $catalogProductConfiguration;
        parent::__construct($context, $data);
    }

    /**
     * Returns helper for product type
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Helper_Product_Configuration_Interface
     */
    protected function _getProductHelper($product)
    {
        // Retrieve whole array of renderers
        $productHelpers = $this->getProductHelpers();
        if (!is_array($productHelpers)) {
            $column = $this->getColumn();
            if ($column) {
                $grid = $column->getGrid();
                if ($grid) {
                    $productHelpers = $grid->getProductConfigurationHelpers();
                    $this->setProductHelpers($productHelpers ? $productHelpers : array());
                }
            }
        }

        // Check whether we have helper for our product
        $productType = $product->getTypeId();
        if (isset($productHelpers[$productType])) {
            $helperName = $productHelpers[$productType];
        } else if (isset($productHelpers['default'])) {
            $helperName = $productHelpers['default'];
        } else {
            $helperName = 'Magento_Catalog_Helper_Product_Configuration';
        }

        $helper = Mage::helper($helperName);
        if (!($helper instanceof Magento_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException(__("Helper for options rendering doesn't implement required interface."));
        }

        return $helper;
    }

    /*
     * Returns product associated with this block
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    /**
     * Returns list of options and their values for product configuration
     *
     * @return array
     */
    protected function getOptionList()
    {
        $item = $this->getItem();
        $product = $item->getProduct();
        $helper = $this->_getProductHelper($product);
        return $helper->getOptions($item);
    }

    /**
     * Returns formatted option value for an item
     *
     * @param Magento_Wishlist_Item_Option
     * @return array
     */
    protected function getFormattedOptionValue($option)
    {
        $params = array(
            'max_length' => 55
        );
        return $this->_catalogProductConfiguration->getFormattedOptionValue($option, $params);
    }

    /*
     * Renders item product name and its configuration
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return string
     */
    public function render(Magento_Object $item)
    {
        $this->setItem($item);
        $product = $this->getProduct();
        $options = $this->getOptionList();
        return $options ? $this->_renderItemOptions($product, $options) : $this->escapeHtml($product->getName());
    }

    /**
     * Render product item with options
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $options
     * @return string
     */
    protected function _renderItemOptions(Magento_Catalog_Model_Product $product, array $options)
    {
        $html = '<div class="bundle-product-options">'
            . '<strong>' . $this->escapeHtml($product->getName()) . '</strong>'
            . '<dl>';
        foreach ($options as $option) {
            $formattedOption = $this->getFormattedOptionValue($option);
            $html .= '<dt>' . $this->escapeHtml($option['label']) . '</dt>';
            $html .= '<dd>' . $this->escapeHtml($formattedOption['value']) . '</dd>';
        }
        $html .= '</dl></div>';

        return $html;
    }
}
