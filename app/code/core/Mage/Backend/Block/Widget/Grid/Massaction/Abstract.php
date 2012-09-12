<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid widget massaction block
 *
 * @method Mage_Sales_Model_Quote setHideFormElement(boolean $value) Hide Form element to prevent IE errors
 * @method boolean getHideFormElement()
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Backend_Block_Widget_Grid_Massaction_Abstract extends Mage_Backend_Block_Widget
{
    /**
     * Massaction items
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Backend helper
     *
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * Sets Massaction template
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setTemplate('Mage_Backend::widget/grid/massaction.phtml');

        $this->_helper = isset($data['helper'])? $data['helper'] : Mage::helper('Mage_Backend_Helper_Data');
        if (!($this->_helper instanceof Mage_Backend_Helper_Data)) {
            throw new InvalidArgumentException('Helper must be instance of Mage_Backend_Helper_Data');
        }
        $this->setErrorText($this->_helper->jsQuoteEscape($this->_helper->__('Please select items.')));
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_prepareMassaction();
    }

    /**
     * Add new massaction item
     *
     * $item = array(
     *      'label'    => string,
     *      'complete' => string, // Only for ajax enabled grid (optional)
     *      'url'      => string,
     *      'confirm'  => string, // text of confirmation of this action (optional)
     *      'additional' => string // (optional)
     * );
     *
     * @param string $itemId
     * @param array|Varien_Object $item
     * @return Mage_Backend_Block_Widget_Grid_Massaction_Abstract
     */
    public function addItem($itemId, $item)
    {
        if (is_array($item)) {
            $item['id'] = $itemId;
            $this->_items[$itemId] = new Varien_Object($item);
        } elseif($item instanceof Varien_Object) {
            $this->_items[$itemId] = $item;
        }

        return $this;
    }

    /**
     * Retrieve massaction item with id $itemId
     *
     * @param string $itemId
     * @return Mage_Backend_Block_Widget_Grid_Massaction_Item
     */
    public function getItem($itemId)
    {
        if(isset($this->_items[$itemId])) {
            return $this->_items[$itemId];
        }

        return null;
    }

    /**
     * Retrieve massaction items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve massaction items JSON
     *
     * @return string
     */
    public function getItemsJson()
    {
        $result = array();
        foreach ($this->getItems() as $itemId=>$item) {
            $result[$itemId] = $item->toArray();
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }

    /**
     * Retrieve massaction items count
     *
     * @return integer
     */
    public function getCount()
    {
        return sizeof($this->_items);
    }

    /**
     * Checks are massactions available
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getCount() > 0 && $this->getMassactionIdField();
    }

    /**
     * Retrieve global form field name for all massaction items
     *
     * @return string
     */
    public function getFormFieldName()
    {
        return ($this->getData('form_field_name') ? $this->getData('form_field_name') : 'massaction');
    }

    /**
     * Retrieve form field name for internal use. Based on $this->getFormFieldName()
     *
     * @return string
     */
    public function getFormFieldNameInternal()
    {
        return  'internal_' . $this->getFormFieldName();
    }

    /**
     * Retrieve massaction block js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve grid block js object name
     *
     * @return string
     */
    public function getGridJsObjectName()
    {
        return $this->getParentBlock()->getJsObjectName();
    }

    /**
     * Retrieve JSON string of selected checkboxes
     *
     * @return string
     */
    public function getSelectedJson()
    {
        if($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return join(',', $selected);
        } else {
            return '';
        }
    }

    /**
     * Retrieve array of selected checkboxes
     *
     * @return array
     */
    public function getSelected()
    {
        if($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return $selected;
        } else {
            return array();
        }
    }

    /**
     * Retrieve apply button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getButtonHtml($this->__('Submit'), $this->getJsObjectName() . ".apply()");
    }

    public function getJavaScript()
    {
        return " var {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', "
                . "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
                . ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
                . "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
                . "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
                . ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
                . ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
                . "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
    }

    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        $gridIds = $this->getParentBlock()->getCollection()->getAllIds();

        if(!empty($gridIds)) {
            return join(",", $gridIds);
        }
        return '';
    }

    public function getHtmlId()
    {
        return $this->getParentBlock()->getHtmlId() . '_massaction';
    }

    /**
     * Remove existing massaction item by its id
     *
     * @param string $itemId
     * @return Mage_Backend_Block_Widget_Grid_Massaction_Abstract
     */
    public function removeItem($itemId)
    {
        if (isset($this->_items[$itemId])) {
            unset($this->_items[$itemId]);
        }

        return $this;
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @return boolean
     */
    public function getUseSelectAll()
    {
        return $this->_getData('use_select_all') === null || $this->_getData('use_select_all');
    }

    /**
     * Retrieve select all functionality flag check
     *
     * @param boolean $flag
     * @return Mage_Backend_Block_Widget_Grid_Massaction_Abstract
     */
    public function setUseSelectAll($flag)
    {
        $this->setData('use_select_all', (bool) $flag);
        return $this;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return Mage_Backend_Block_Widget_Grid_Massaction
     */
    protected function _prepareMassaction()
    {
        $options = $this->getOptions();
        if (null !== $options) {
            foreach ($this->getOptions() as $optionId => $option) {
                $this->addItem($optionId, $option);
            }
        }

        return $this;
    }

    /**
     * Prepare grid massaction column
     *
     * @return Mage_Backend_Block_Widget_Grid_Extended
     */
    protected function _prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Column')
            ->setData(array(
            'index'        => $this->getMassactionIdField(),
            'filter_index' => $this->getMassactionIdFilter(),
            'type'         => 'massaction',
            'name'         => $this->getFormFieldName(),
            'align'        => 'center',
            'is_system'    => true
        ));

        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }

        $gridBlock = $this->getParentBlock();
        $massactionColumn->setSelected($this->getSelected())
            ->setGrid($gridBlock)
            ->setId($columnId);

        $columnSetBlock = $gridBlock->getColumnSet();
        $columnSetBlock->insert($massactionColumn, count($columnSetBlock->getColumns())+1, false, $columnId);
        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if($this->isAvailable()) {
            $this->_prepareMassactionColumn();
        }

        return parent::_beforeToHtml();
    }

}
