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
 * Customers defined options
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options;

use Magento\Backend\Block\Widget;
use Magento\Catalog\Model\Product;

class Option extends Widget
{
    /**
     * @var Product
     */
    protected $_productInstance;

    /**
     * @var \Magento\Object[]
     */
    protected $_values;

    /**
     * @var int
     */
    protected $_itemCount = 1;

    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/options/option.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     */
    protected $_productOptionConfig;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $_configYesNo;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Product\Options\Type
     */
    protected $_optionType;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Config\Source\Yesno $configYesNo
     * @param \Magento\Catalog\Model\Config\Source\Product\Options\Type $optionType
     * @param Product $product
     * @param \Magento\Registry $registry
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Config\Source\Yesno $configYesNo,
        \Magento\Catalog\Model\Config\Source\Product\Options\Type $optionType,
        Product $product,
        \Magento\Registry $registry,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        array $data = array()
    ) {
        $this->_optionType = $optionType;
        $this->_configYesNo = $configYesNo;
        $this->_product = $product;
        $this->_productOptionConfig = $productOptionConfig;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->_itemCount;
    }

    /**
     * @param int $itemCount
     * @return $this
     */
    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }

    /**
     * Get Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            $product = $this->_coreRegistry->registry('product');
            if ($product) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = $this->_product;
            }
        }

        return $this->_productInstance;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_productInstance = $product;
        return $this;
    }

    /**
     * Retrieve options field name prefix
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'product[options]';
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'product_option';
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getOptionsReadonly();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        foreach ($this->_productOptionConfig->getAll() as $option) {
            $this->addChild($option['name'] . '_option_type', $option['renderer']);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()->getBlock('admin.product.options')->getChildBlock('add_button')->getId();
        return $buttonId;
    }

    /**
     * @return mixed
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldId() . '_${id}_type',
                'class' => 'select select-product-option-type required-option-select'
            )
        )->setName(
            $this->getFieldName() . '[${id}][type]'
        )->setOptions(
            $this->_optionType->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * @return mixed
     */
    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\View\Element\Html\Select'
        )->setData(
            array('id' => $this->getFieldId() . '_${id}_is_require', 'class' => 'select')
        )->setName(
            $this->getFieldName() . '[${id}][is_require]'
        )->setOptions(
            $this->_configYesNo->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $canEditPrice = $this->getCanEditPrice();
        $canReadPrice = $this->getCanReadPrice();
        $this->getChildBlock('select_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('file_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('date_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('text_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $templates = $this->getChildHtml(
                'text_option_type'
            ) . "\n" . $this->getChildHtml(
                'file_option_type'
            ) . "\n" . $this->getChildHtml(
                'select_option_type'
            ) . "\n" . $this->getChildHtml(
                'date_option_type'
            );

        return $templates;
    }

    /**
     * @return \Magento\Object[]
     */
    public function getOptionValues()
    {
        $optionsArr = $this->getProduct()->getOptions();

        if (!$this->_values || $this->getIgnoreCaching()) {
            $showPrice = $this->getCanReadPrice();
            $values = array();
            $scope = (int)$this->_storeConfig->getValue(
                \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($optionsArr as $option) {
                /* @var $option \Magento\Catalog\Model\Product\Option */

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->escapeHtml($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                        $option->getOptionId(),
                        'title',
                        is_null($option->getStoreTitle())
                    );
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
                }

                if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value \Magento\Catalog\Model\Product\Option\Value */
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->escapeHtml($_value->getTitle()),
                            'price' => $showPrice ? $this->getPriceValue(
                                $_value->getPrice(),
                                $_value->getPriceType()
                            ) : '',
                            'price_type' => $showPrice ? $_value->getPriceType() : 0,
                            'sku' => $this->escapeHtml($_value->getSku()),
                            'sort_order' => $_value->getSortOrder()
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                $_value->getOptionId(),
                                'title',
                                is_null($_value->getStoreTitle()),
                                $_value->getOptionTypeId()
                            );
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null(
                                $_value->getStoreTitle()
                            ) ? 'disabled' : null;
                            if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(),
                                    'price',
                                    is_null($_value->getstorePrice()),
                                    $_value->getOptionTypeId()
                                );
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null(
                                    $_value->getStorePrice()
                                ) ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = $showPrice ? $this->getPriceValue(
                        $option->getPrice(),
                        $option->getPriceType()
                    ) : '';
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->escapeHtml($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0'
                        && $scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE
                    ) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                            $option->getOptionId(),
                            'price',
                            is_null($option->getStorePrice())
                        );
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
                    }
                }
                $values[] = new \Magento\Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

    /**
     * Retrieve html of scope checkbox
     *
     * @param string $id
     * @param string $name
     * @param boolean $checked
     * @param string $select_id
     * @return string
     */
    public function getCheckboxScopeHtml($id, $name, $checked = true, $select_id = '-1')
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($select_id != '-1') {
            $selectNameHtml = '[values][' . $select_id . ']';
            $selectIdHtml = 'select_' . $select_id . '_';
        }
        $useDefault =
            '<div class="field-service">' . '<label for="' . $this->getFieldId() . '_' . $id . '_' . $selectIdHtml
            . $name . '" class="use-default">' . '<input value="1" type="checkbox" class="use-default-control"'
            . 'name="' . $this->getFieldName() . '[' . $id . ']' . $selectNameHtml . '[scope][' . $name . ']"' . 'id="'
            . $this->getFieldId() . '_' . $id . '_' . $selectIdHtml . $name . '_use_default"' . $checkedHtml
            . ' /><span class="use-default-label">' . __(
                'Use Default'
            ) . '</span></label></div>';

        return $useDefault;
    }

    /**
     * @param float $value
     * @param string $type
     * @return string
     */
    public function getPriceValue($value, $type)
    {
        if ($type == 'percent') {
            return number_format($value, 2, null, '');
        } elseif ($type == 'fixed') {
            return number_format($value, 2, null, '');
        }
    }

    /**
     * Return product grid url for custom options import popup
     *
     * @return string
     */
    public function getProductGridUrl()
    {
        return $this->getUrl('catalog/*/optionsImportGrid');
    }

    /**
     * Return custom options getter URL for ajax queries
     *
     * @return string
     */
    public function getCustomOptionsUrl()
    {
        return $this->getUrl('catalog/*/customOptions');
    }
}
