<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer giftregistry list block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
abstract class Enterprise_GiftRegistry_Block_Customer_Edit_Abstract extends Mage_Directory_Block_Data
{

    /**
     * Registry Entity object
     *
     * @var Enterprise_GiftRegistry_Model_Entity
     */
    protected $_entity = null;

    /**
     * Attribute groups array
     *
     * @var array
     */
    protected $_groups = null;

    /**
     * Static types fields holder
     *
     * @var array
     */
    protected $_staticTypes = array();

    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Getter, return entity object , instantiated in controller
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('enterprise_giftregistry_entity');
    }

    /**
     * Getter for CustomAttributes Array
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->getEntity()->getCustomAttributes();
    }

    /**
     * Return array of attribute groups for using as options
     *
     * @return array
     */
    public function getAttributeGroups()
    {

        return Mage::getSingleton('enterprise_giftregistry/attribute_config')->getAttributeGroups();
    }

    /**
     * Return group label
     *
     * @param string $groupId
     * @return string
     */
    public function getGroupLabel($groupId)
    {
        if ($this->_groups === null) {
            $this->_groups = Mage::getSingleton('enterprise_giftregistry/attribute_config')->getAttributeGroups();
        }
        if (is_array($this->_groups) && (!empty($this->_groups[$groupId]))
            && is_array($this->_groups[$groupId]) && !empty($this->_groups[$groupId]['label'])) {
            $label = $this->_groups[$groupId]['label'];
        } else {
            $label = $groupId;
        }
        return $label;
    }

    /**
     * JS Calendar html
     *
     * @param string name   - DOM name
     * @param string id     - DOM id
     * @param sting format  - full|long|medium|short
     *
     * @return string
     */
    public function getCalendarDateHtml($name, $id, $format = false)
    {
        if ($format === false) {
            $format = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
        }
        $calendar = $this->getLayout()
            ->createBlock('core/html_date')
            ->setId($id)
            ->setName($name)
            ->setClass('product-custom-option datetime-picker input-text')
            ->setImage($this->getSkinUrl('images/calendar.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateStrFormat($format));
        return $calendar->getHtml();
    }

    /**
     * Select element for choosing attribute group
     *
     * @return string
     */
    public function getSelectHtml($options, $name, $id, $value = false)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setData(array(
                'id'    => $id,
                'class' => 'select required-entry global-scope'
            ))
            ->setName($name)
            ->setValue($value)
            ->setOptions($options);
        return $select->getHtml();
    }

    /**
     * Reorder attributes array  by group
     *
     * @param array $attributes
     * @return array
     */
    protected function _groupAttributes($attributes)
    {
        $grouped = array();
        if (is_array($attributes)) {
            foreach ($attributes as $field => $fdata){
                if (is_array($fdata)) {
                    $grouped[$fdata['group']][$field] = $fdata;
                    $grouped[$fdata['group']][$field]['id'] = $field;
                }
            }
        }
        return $grouped;
    }

    /**
     * Get current type Id
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->getEntity()->getTypeId();
    }

    /**
     * Get current type label
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->getEntity()->getTypeLabel();
    }

    /**
     * Reorder data in group array for internal use
     *
     * @param array $selectOptions
     * @return array
     */
    protected function _convertGroupArray($selectOptions)
    {
        $data = array();
        if (is_array($selectOptions)) {
            foreach ($selectOptions as $k => $option) {
                $data[$k] = array('label' => $option['label'], 'value' => $option['code']);
            }
        }
        return $data;
    }

    /**
     * Render input field of the specific type : text, select, date, region, country
     * @param array  data    - description how to render this field
     * @param string name   - field DOM name
     * @param string id     - field DOM id
     * @param value  string  - preseted value
     * @param class  string  - class
     *
     * @return string
     */
    public function renderField($data, $name, $id, $value = null, $class = null)
    {
        return $this->_renderField($data, $name, $id, $value, $class);
    }

    /**
     * Render input field of the specific type : text, select, date, region, country
     * @param array  data    - description how to render this field
     * @param string name   - field DOM name
     * @param string id     - field DOM id
     * @param value  string  - preseted value
     * @param class  string  - class
     *
     * @return string
     */
    public function _renderField($data, $name, $id, $value = null, $class = null)
    {
        if (is_array($data) && (count($data)) && $id) {
            $type = $data['type'];
            if (!in_array($type, $this->_staticTypes)) {
                $name = $this->_prefix. '[' . $name . ']';
            }
            if ($type == 'event_country_code') {
                return $this->getCountryHtmlSelect($value, 'event_country_code', 'event_country_code');
                //    public function getCountryHtmlSelect($defValue=null, $name='country_id', $id='country', $title='Country')
            } else if ($type == 'event_region_code') {
                $this->setRegionJsVisible(true);
                return $this->getRegionHtmlSelectEmpty('event_region', 'event_region', $value, 'required-entry',
                    $this->__('State/Province'))
                    . $this->_getInputTextHtml('event_region_text', 'event_region_text', '', ' input-text '
                        , 'title="' . $this->__('State/Province') . '" style="display:none;"');
            } else if ($type == 'event_location' || $type == 'text') {
                return $this->_getInputTextHtml($name, $id, $value, $class . ' input-text');
            } else if ($type == 'event_date' || $type == 'date') {
                return $this->getCalendarDateHtml($name, $id);
            } else if ($type == 'select') {
                $options  = $data['options'];
                return $this->getSelectHtml($this->_convertGroupArray($options), $name, $id, $value);
            } else {
                return $this->_getInputTextHtml($name, $id, $value, $class . ' input-text');
            }
        }
    }

    /**
     * Render "input text" field
     * @param string $name
     * @param string $id
     * @param string $value
     * @param string $class
     * @param string $params additional params
     *
     * @return string
     */
    protected function _getInputTextHtml($name, $id, $value = '', $class = '', $params = '')
    {

        $template = $this->getLayout()->getBlock('giftregistry_edit')->getInputTypeTemplate('text');
        $this->setInputName($name)
            ->setInputId($id)
            ->setInputValue($value)
            ->setInputClass($class)
            ->setInputParams($params);
        if ($template) {
            $this->setScriptPath(Mage::getBaseDir('design'));
            return  $this->fetchView($template);
        }
    }

    /**
     * Return region select html element
     * @param string $name
     * @param string $id
     * @param string $value
     * @param string $class
     * @param string $params additional params
     */
    public function getRegionHtmlSelectEmpty($name, $id, $value = '', $class = '', $params = '', $default = '')
    {
        $template = $this->getLayout()->getBlock('giftregistry_edit')->getInputTypeTemplate('region');
        $this->setSelectRegionName($name)
            ->setSelectRegionId($id)
            ->setSelectRegionValue($value)
            ->setSelectRegionClass($class)
            ->setSelectRegionParams($params)
            ->setSelectRegionDefault($default);
        if ($template) {
            $this->setScriptPath(Mage::getBaseDir('design'));
            return  $this->fetchView($template);
        }
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCreateActionUrl($this->getUrl('enterprise_giftregistry/index/addPost'));
        return parent::_toHtml();
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getAddGiftRegistryUrl()
    {
        return $this->getUrl('enterprise_giftregistry/index/addselect');
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getSaveActionUrl()
    {
        return $this->getUrl('enterprise_giftregistry/index/save');
    }

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedAttributes()
    {
        $attributes = $this->getCustomAttributes();
        if (!empty($attributes[$this->_prefix])) {
            return $this->_groupAttributes($attributes[$this->_prefix]);
        }
        return array();
    }
}
