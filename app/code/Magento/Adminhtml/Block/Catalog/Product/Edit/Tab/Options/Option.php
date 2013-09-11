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
 * customers defined options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options;

class Option extends \Magento\Adminhtml\Block\Widget
{
    protected $_product;

    protected $_productInstance;

    protected $_values;

    protected $_itemCount = 1;

    protected $_template = 'catalog/product/edit/options/option.phtml';

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    public function getItemCount()
    {
        return $this->_itemCount;
    }

    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }

    /**
     * Get Product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = \Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = \Mage::getSingleton('Magento\Catalog\Model\Product');
            }
        }

        return $this->_productInstance;
    }

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
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getOptionsReadonly();
    }

    protected function _prepareLayout()
    {
        $path = 'global/catalog/product/options/custom/groups';

        foreach (\Mage::getConfig()->getNode($path)->children() as $group) {
            $this->addChild(
                $group->getName() . '_option_type',
                (string)\Mage::getConfig()->getNode($path . '/' . $group->getName() . '/render')
            );
        }

        return parent::_prepareLayout();
    }

    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
            ->getBlock('admin.product.options')
            ->getChildBlock('add_button')->getId();
        return $buttonId;
    }

    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
            ->setData(array(
                'id'    => $this->getFieldId() . '_${id}_type',
                'class' => 'select select-product-option-type required-option-select',
            ))
            ->setName($this->getFieldName() . '[${id}][type]')
            ->setOptions(\Mage::getSingleton('Magento\Catalog\Model\Config\Source\Product\Options\Type')->toOptionArray());

        return $select->getHtml();
    }

    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
            ->setData(array(
                'id'    => $this->getFieldId() . '_${id}_is_require',
                'class' => 'select'
            ))
            ->setName($this->getFieldName() . '[${id}][is_require]')
            ->setOptions(\Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray());

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
        $this->getChildBlock('select_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChildBlock('file_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChildBlock('date_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $this->getChildBlock('text_option_type')
            ->setCanReadPrice($canReadPrice)
            ->setCanEditPrice($canEditPrice);

        $templates = $this->getChildHtml('text_option_type') . "\n" .
            $this->getChildHtml('file_option_type') . "\n" .
            $this->getChildHtml('select_option_type') . "\n" .
            $this->getChildHtml('date_option_type');

        return $templates;
    }

    public function getOptionValues()
    {
        $optionsArr = $this->getProduct()->getOptions();

        if (!$this->_values || $this->getIgnoreCaching()) {
            $showPrice = $this->getCanReadPrice();
            $values = array();
            $scope = (int)\Mage::app()->getStore()->getConfig(\Magento\Core\Model\Store::XML_PATH_PRICE_SCOPE);
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
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'title',
                        is_null($option->getStoreTitle()));
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
                            'price' => ($showPrice)
                                ? $this->getPriceValue($_value->getPrice(), $_value->getPriceType()) : '',
                            'price_type' => ($showPrice) ? $_value->getPriceType() : 0,
                            'sku' => $this->escapeHtml($_value->getSku()),
                            'sort_order' => $_value->getSortOrder(),
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                $_value->getOptionId(), 'title', is_null($_value->getStoreTitle()),
                                $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle())
                                ? 'disabled' : null;
                            if ($scope == \Magento\Core\Model\Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(), 'price', is_null($_value->getstorePrice()),
                                    $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice())
                                    ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = ($showPrice)
                        ? $this->getPriceValue($option->getPrice(), $option->getPriceType()) : '';
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->escapeHtml($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0'
                        && $scope == \Magento\Core\Model\Store::PRICE_SCOPE_WEBSITE
                    ) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'price',
                            is_null($option->getStorePrice()));
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
        $useDefault = '<div class="field-service">'
            . '<label for="' . $this->getFieldId() . '_' . $id . '_' . $selectIdHtml . $name . '" class="use-default">'
            . '<input value="1" type="checkbox" class="use-default-control"'
            . 'name="' . $this->getFieldName() . '[' . $id . ']' . $selectNameHtml . '[scope][' . $name . ']"'
            . 'id="' . $this->getFieldId() . '_' . $id . '_' . $selectIdHtml . $name . '_use_default"' . $checkedHtml
            .' /><span class="use-default-label">' . __('Use Default')
            . '</span></label></div>';

        return $useDefault;
    }

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
        return $this->getUrl('*/*/optionsImportGrid');
    }

    /**
     * Return custom options getter URL for ajax queries
     *
     * @return string
     */
    public function getCustomOptionsUrl()
    {
        return $this->getUrl('*/*/customOptions');
    }
}
