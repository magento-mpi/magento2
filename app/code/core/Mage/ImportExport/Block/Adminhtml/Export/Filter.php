<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export filter block
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Export_Filter extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Helper object.
     *
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Set grid parameters.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_helper = Mage::helper('importexport');

        $this->setRowClickCallback(null);
        $this->setId('export_filter_grid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setPagerVisibility(false);
        $this->setDefaultLimit(null);
        $this->setUseAjax(true);
    }

    /**
     * Date 'from-to' filter HTML.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getDateFromToHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $dateBlock = new Mage_Core_Block_Html_Date(array(
            'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
            'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
            'class'        => 'input-text',
            'format'       => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'extra_params' => 'style="width:85px !important"',
            'image'        => $this->getSkinUrl('images/grid-cal.gif')
        ));
        return '<strong>' . $this->_helper->__('From') . ':</strong>&nbsp;' . $dateBlock->getHtml()
             . '&nbsp;<strong>' . $this->_helper->__('To') . ':</strong>&nbsp;'
             . $dateBlock->setId($dateBlock->getId() . '_to')->getHtml();
    }

    /**
     * Input text filter HTML.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getInputHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        return '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '" class="input-text" style="width:274px;"/>';
    }

    /**
     * Multiselect field filter HTML.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getMultiSelectHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->getFilterOptions()) {
            $options = $attribute->getFilterOptions();
        } else {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $optionParams) {
                if ('' === $optionParams['value']) {
                    unset($options[$key]);
                    break;
                }
            }
        }
        if (($size = count($options))) {
            $selectBlock = new Mage_Core_Block_Html_Select(array(
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()). '[]',
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'multiselect',
                'extra_params' => 'multiple="multiple" size="' . ($size > 5 ? 5 : ($size < 2 ? 2 : $size))
                                . '" style="width:280px"'
            ));
            return $selectBlock->setOptions($options)->getHtml();
        } else {
            return $this->_helper->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Number 'from-to' field filter HTML.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getNumberFromToHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $name = $this->getFilterElementName($attribute->getAttributeCode());
        return '<strong>' . $this->_helper->__('From') . ':</strong>&nbsp;'
             . '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '[]" class="input-text" style="width:100px;"/>&nbsp;<strong>' . $this->_helper->__('To')
             . ':</strong>&nbsp;<input type="text" name="' . $name
             . '[]" class="input-text" style="width:100px;"/>';
    }

    /**
     * Select field filter HTML.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getSelectHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->getFilterOptions()) {
            $options = array();

            foreach ($attribute->getFilterOptions() as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
        } else {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        if (($size = count($options))) {
            // add empty vaue option
            $firstOption = reset($options);

            if ('' === $firstOption['value']) {
                $options[key($options)]['label'] = '';
            } else {
                array_unshift($options, array('value' => '', 'label' => ''));
            }
            $selectBlock = new Mage_Core_Block_Html_Select(array(
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()),
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'select',
                'extra_params' => 'style="width:280px"'
            ));
            return $selectBlock->setOptions($options)->getHtml();
        } else {
            return $this->_helper->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('skip', array(
            'header'     => $this->_helper->__('Skip'),
            'type'       => 'checkbox',
            'name'       => 'skip',
            'field_name' => Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP . '[]',
            'filter'     => false,
            'sortable'   => false,
            'align'      => 'center',
            'index'      => 'attribute_id'
        ));
        $this->addColumn('frontend_label', array(
            'header'   => $this->_helper->__('Attribute Label'),
            'index'    => 'frontend_label',
            'sortable' => false,
        ));
        $this->addColumn('attribute_code', array(
            'header' => $this->_helper->__('Attribute Code'),
            'index'  => 'attribute_code'
        ));
        $this->addColumn('filter', array(
            'header'         => $this->_helper->__('Filter'),
            'sortable'       => false,
            'filter'         => false,
            'frame_callback' => array($this, 'decorateFilter')
        ));

        return $this;
    }

    /**
     * Create filter fields for 'Filter' column.
     *
     * @param mixed $value
     * @param Mage_Eav_Model_Entity_Attribute $row
     * @param Varien_Object $column
     * @param boolean $isExport
     * @return string
     */
    public function decorateFilter($value, Mage_Eav_Model_Entity_Attribute $row, Varien_Object $column, $isExport)
    {
        switch (Mage_ImportExport_Model_Export::getAttributeFilterType($row)) {
            case Mage_ImportExport_Model_Export::FILTER_TYPE_SELECT:
                $cell = $this->_getSelectHtml($row);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_INPUT:
                $cell = $this->_getInputHtml($row);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_DATE:
                $cell = $this->_getDateFromToHtml($row);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_NUMBER:
                $cell = $this->_getNumberFromToHtml($row);
                break;
            default:
                $cell = $this->_helper->__('Unknown attribute filter type');
        }
        return $cell;
    }

    /**
     * Element filter ID getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementId($attributeCode)
    {
        return Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "_{$attributeCode}";
    }

    /**
     * Element filter full name getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementName($attributeCode)
    {
        return Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "[{$attributeCode}]";
    }

    /**
     * Get row edit URL.
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Prepare collection by setting page number, sorting etc..
     *
     * @param Mage_Eav_Model_Mysql4_Entity_Attribute_Collection $collection
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function prepareCollection(Mage_Eav_Model_Mysql4_Entity_Attribute_Collection $collection)
    {
        $this->_collection = $collection;

        $this->_prepareGrid();

        return $this->_collection;
    }
}
