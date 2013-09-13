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
 * Adminhtml catalog super product configurable tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super;

class Config
    extends \Magento\Backend\Block\Widget
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'catalog/product/edit/super/config.phtml';

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_app = $app;
        $this->_locale = $locale;
    }

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setProductId($this->getRequest()->getParam('id'));

        $this->setId('config_super_product');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Retrieve Tab class (for loading)
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return (bool) $this->getProduct()->getCompositeReadonly();
    }

    /**
     * Check whether attributes of configurable products can be editable
     *
     * @return boolean
     */
    public function isAttributesConfigurationReadonly()
    {
        return (bool) $this->getProduct()->getAttributesConfigurationReadonly();
    }

    /**
     * Get configurable product type
     *
     * @return \Magento\Catalog\Model\Product\Type\Configurable
     */
    protected function _getProductType()
    {
        return \Mage::getModel('Magento\Catalog\Model\Product\Type\Configurable');
    }

    /**
     * Check whether prices of configurable products can be editable
     *
     * @return boolean
     */
    public function isAttributesPricesReadonly()
    {
        return $this->getProduct()->getAttributesConfigurationReadonly() ||
            (\Mage::helper('Magento\Catalog\Helper\Data')->isPriceGlobal() && $this->isReadonly());
    }

    /**
     * Prepare Layout data
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config
     */
    protected function _prepareLayout()
    {
        $this->addChild('create_empty', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label' => __('Create Empty'),
            'class' => 'add',
            'onclick' => 'superProduct.createEmptyProduct()'
        ));
        $this->addChild('super_settings', 'Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Settings');

// @todo: Remove unused code and blocks
//        if ($this->getProduct()->getId()) {
//            $this->setChild('simple',
//                $this->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Simple',
//                    'catalog.product.edit.tab.super.config.simple')
//            );
//
//            $this->addChild('create_from_configurable', '\Magento\Adminhtml\Block\Widget\Button', array(
//                'label' => __('Copy From Configurable'),
//                'class' => 'add',
//                'onclick' => 'superProduct.createNewProduct()'
//            ));
//        }

        $this->addChild(
            'generate',
            '\Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Generate Variations'),
                'class' => 'generate',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'generate',
                            'target' => '#product-variations-matrix',
                            'eventData' => array(
                                'url' => $this->getUrl('*/*/generateVariations', array('_current' => true)),
                            ),
                        ),
                    ),
                    'action' => 'generate',
                ),
            )
        );
        $this->addChild(
            'add_attribute',
            '\Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Create New Variation Set'),
                'class' => 'new-variation-set',
                'data_attribute' => array(
                    'mage-init' => array(
                        'configurableAttribute' => array(
                            'url' => $this->getUrl(
                                '*/catalog_product_attribute/new',
                                array(
                                    'store' => $this->getProduct()->getStoreId(),
                                    'product_tab' => 'variations',
                                    'popup' => 1,
                                    '_query' => array(
                                        'attribute' => array(
                                            'is_global' => 1,
                                            'frontend_input' => 'select',
                                            'is_configurable' => 1
                                        ),
                                    )
                                )
                            )
                        )
                    )
                ),
            )
        );
        $this->addChild(
            'add_option',
            '\Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Option'),
                'class' => 'action- scalable add',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'add-option'),
                    ),
                    'action' => 'add-option',
                ),
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return \Mage::registry('current_product');
    }

    /**
     * Retrieve attributes data
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->hasData('attributes')) {
            $attributes = (array)$this->_getProductType()->getConfigurableAttributesAsArray($this->getProduct());
            $productData = (array)$this->getRequest()->getParam('product');
            if (isset($productData['configurable_attributes_data'])) {
                $configurableData = $productData['configurable_attributes_data'];
                foreach ($attributes as $key => &$attribute) {
                    if (isset($configurableData[$key])) {
                        $attribute['values'] = array_merge(
                            isset($attribute['values']) ? $attribute['values'] : array(),
                            isset($configurableData[$key]['values'])
                                ? array_filter($configurableData[$key]['values'])
                                : array()
                        );
                    }
                }
            }

            foreach ($attributes as &$attribute) {
                if (isset($attribute['values']) && is_array($attribute['values'])) {
                    foreach ($attribute['values'] as &$attributeValue) {
                        if (!$this->getCanReadPrice()) {
                            $attributeValue['pricing_value'] = '';
                            $attributeValue['is_percent'] = 0;
                        }
                        $attributeValue['can_edit_price'] = $this->getCanEditPrice();
                        $attributeValue['can_read_price'] = $this->getCanReadPrice();
                    }
                }
            }
            $this->setData('attributes', $attributes);
        }
        return $this->getData('attributes');
    }

    /**
     * Retrieve Links in JSON format
     *
     * @return string
     */
    public function getLinksJson()
    {
        $products = $this->_getProductType()
            ->getUsedProducts($this->getProduct());
        if(!$products) {
            return '{}';
        }
        $data = array();
        foreach ($products as $product) {
            $data[$product->getId()] = $this->getConfigurableSettings($product);
        }
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($data);
    }

    /**
     * Retrieve configurable settings
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getConfigurableSettings($product) {
        $data = array();
        $attributes = $this->_getProductType()
            ->getUsedProductAttributes($this->getProduct());
        foreach ($attributes as $attribute) {
            $data[] = array(
                'attribute_id' => $attribute->getId(),
                'label'        => $product->getAttributeText($attribute->getAttributeCode()),
                'value_index'  => $product->getData($attribute->getAttributeCode())
            );
        }

        return $data;
    }

    /**
     * Retrieve Grid child HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Retrieve Grid JavaScript object name
     *
     * @return string
     */
    public function getGridJsObject()
    {
        return $this->getChildBlock('grid')->getJsObjectName();
    }

    /**
     * Retrieve Create New Empty Product URL
     *
     * @return string
     */
    public function getNewEmptyProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->getProduct()->getAttributeSetId(),
                'type'     => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1
            )
        );
    }

    /**
     * Retrieve Create New Product URL
     *
     * @return string
     */
    public function getNewProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->getProduct()->getAttributeSetId(),
                'type'     => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->getProduct()->getId()
            )
        );
    }

    /**
     * Retrieve Required attributes Ids (comma separated)
     *
     * @return string
     */
    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        $configurableAttributes = $this->getProduct()
            ->getTypeInstance()->getConfigurableAttributes($this->getProduct());
        foreach ($configurableAttributes as $attribute) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Associated Products');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Associated Products');
    }

    /**
     * Can show tab flag
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check is a hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Show "Use default price" checkbox
     *
     * @return bool
     */
    public function getShowUseDefaultPrice()
    {
        return !\Mage::helper('Magento\Catalog\Helper\Data')->isPriceGlobal()
            && $this->getProduct()->getStoreId();
    }

    /**
     * Get list of used attributes
     *
     * @return array
     */
    public function getSelectedAttributes()
    {
        return $this->getProduct()->isConfigurable()
            ? array_filter($this->_getProductType()->getUsedProductAttributes($this->getProduct()))
            : array();
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }

    /**
     * @return \Magento\Core\Model\App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Get base application currency
     *
     * @return \Zend_Currency
     */
    public function getBaseCurrency()
    {
        return $this->getLocale()->currency($this->getApp()->getBaseCurrencyCode());
    }
}
