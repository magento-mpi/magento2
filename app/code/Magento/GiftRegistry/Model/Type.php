<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry types processing model
 *
 * @method \Magento\GiftRegistry\Model\Resource\Type _getResource()
 * @method \Magento\GiftRegistry\Model\Resource\Type getResource()
 * @method string getCode()
 * @method \Magento\GiftRegistry\Model\Type setCode(string $value)
 * @method string getMetaXml()
 * @method \Magento\GiftRegistry\Model\Type setMetaXml(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model;

class Type extends \Magento\Core\Model\AbstractModel
{
    protected $_store = null;
    protected $_storeData = null;

    /**
     * Intialize model
     */
    protected function _construct()
    {
        $this->_init('\Magento\GiftRegistry\Model\Resource\Type');
    }

    /**
     * Perform actions before object save.
     */
    protected function _beforeSave()
    {
        if (!$this->hasStoreId() && !$this->getStoreId()) {
            $this->_cleanupData();
            $xmlModel = \Mage::getModel('\Magento\GiftRegistry\Model\Attribute\Processor');
            $this->setMetaXml($xmlModel->processData($this));
        }

        parent::_beforeSave();
    }

    /**
     * Perform actions after object save.
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveTypeStoreData($this);
        if ($this->getStoreId()) {
            $this->_saveAttributeStoreData();
        }
    }

    /**
     * Perform actions after object load
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->assignAttributesStoreData();
        return $this;
    }

    /**
     * Callback function for sorting attributes by sort_order param
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortAttributes($a, $b)
    {
        if ($a['sort_order'] != $b['sort_order']) {
            return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
        }
        return 0;
    }

    /**
     * Set store id
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = \Mage::app()->getStore($storeId);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->setStoreId();
        }

        return $this->_store;
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Save registry type attribute data per store view
     *
     * @param \Magento\Core\Model\AbstractModel $object
     */
    protected function _saveAttributeStoreData()
    {
        if ($groups = $this->getAttributes()) {
            foreach((array)$groups as $attributes) {
                foreach((array)$attributes as $attribute) {
                    $this->_getResource()->saveStoreData($this, $attribute);
                    if (isset($attribute['options']) && is_array($attribute['options'])) {
                        foreach($attribute['options'] as $option) {
                            $optionCode = $option['code'];
                            $option['code'] = $attribute['code'];
                            $this->_getResource()->saveStoreData($this, $option, $optionCode);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Clear object model from data that should be deleted
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    protected function _cleanupData()
    {
        if ($groups = $this->getAttributes()) {
            $attributesToSave = array();
            $config = \Mage::getSingleton('Magento\GiftRegistry\Model\Attribute\Config');
            foreach ((array)$groups as $group => $attributes) {
                foreach ((array)$attributes as $attribute) {
                    if ($attribute['is_deleted']) {
                        $this->_getResource()->deleteAttributeStoreData($this->getId(), $attribute['code']);
                        if (in_array($attribute['code'], $config->getStaticTypesCodes())) {
                            $this->_getResource()->deleteAttributeValues(
                                $this->getId(),
                                $attribute['code'],
                                $config->isRegistrantAttribute($attribute['code'])
                            );
                        }
                    } else {
                        if (isset($attribute['options']) && is_array($attribute['options'])) {
                            $optionsToSave = array();
                            foreach ($attribute['options'] as $option) {
                                if ($option['is_deleted']) {
                                  $this->_getResource()->deleteAttributeStoreData(
                                      $this->getId(), $attribute['code'], $option['code']
                                  );
                                } else {
                                    $optionsToSave[] = $option;
                                }
                            }
                            $attribute['options'] = $optionsToSave;
                        }
                        $attributesToSave[$group][] = $attribute;
                    }
                }
                $this->setAttributes($attributesToSave);
            }
        }
        return $this;
    }

    /**
     * Assign attributes store data
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function assignAttributesStoreData()
    {
        $xmlModel = \Mage::getModel('\Magento\GiftRegistry\Model\Attribute\Processor');
        $groups = $xmlModel->processXml($this->getMetaXml());
        $storeData = array();

        if (is_array($groups)) {
            foreach ($groups as $group => $attributes) {
                if (!empty($attributes)) {
                    $storeData[$group] = $this->getAttributesStoreData($attributes);
                }
            }
        }
        $this->setAttributes($storeData);
        return $this;
    }

    /**
     * Assign attributes store data
     *
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function getAttributesStoreData($attributes)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $code => $attribute) {
                if ($storeLabel = $this->getAttributeStoreData($code)) {
                    $attributes[$code]['label'] = $storeLabel;
                    $attributes[$code]['default_label'] = $attribute['label'];
                }
                if (isset($attribute['options']) && is_array($attribute['options'])) {
                    $options = array();
                    foreach ($attribute['options'] as $key => $label) {
                        $data = array('code' => $key, 'label' => $label);
                        if ($storeLabel = $this->getAttributeStoreData($code, $key)) {
                            $data['label'] = $storeLabel;
                            $data['default_label'] = $label;
                        }
                        $options[] = $data;
                    }
                    $attributes[$code]['options'] = $options;
                }
            }
            uasort($attributes, array($this, '_sortAttributes'));
        }
        return $attributes;
    }

    /**
     * Retrieve attribute store label
     *
     * @param string $attributeCode
     * @param string $optionCode
     * @return string
     */
    public function getAttributeStoreData($attributeCode, $optionCode = '')
    {
        if ($this->_storeData === null) {
            $this->_storeData = $this->_getResource()->getAttributesStoreData($this);
        }

        if (is_array($this->_storeData)) {
            foreach ($this->_storeData as $item) {
               if ($item['attribute_code'] == $attributeCode && $item['option_code'] == $optionCode) {
                   return $item['label'];
               }
            }
        }
        return '';
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $code
     * @return null|array
     */
    public function getAttributeByCode($code)
    {
        if (!$this->getId() || empty($code)) {
            return null;
        }
        if ($groups = $this->getAttributes()) {
            foreach ($groups as $group) {
                if (isset($group[$code])) {
                    return $group[$code];
                }
            }
        }
        return null;
    }

    /**
     * Retrieve attribute label by code
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeLabel($attributeCode)
    {
        $attribute = $this->getAttributeByCode($attributeCode);
        if ($attribute && isset($attribute['label'])) {
            return $attribute['label'];
        }
        return '';
    }

    /**
     * Retrieve attribute option label by code
     *
     * @param string $attributeCode
     * @param string $optionCode
     * @return string
     */
    public function getOptionLabel($attributeCode, $optionCode)
    {
        $attribute = $this->getAttributeByCode($attributeCode);
        if ($attribute && isset($attribute['options']) && is_array($attribute['options'])) {
            foreach ($attribute['options'] as $option) {
                if ($option['code'] == $optionCode) {
                    return $option['label'];
                }
            }
        }
        return '';
    }

    /**
     * Retrieve listed static attributes list from type attributes list
     *
     * @return array
     */
    public function getListedAttributes()
    {
        $listedAttributes = array();
        if ($this->getAttributes()) {
            $staticCodes = \Mage::getSingleton('Magento\GiftRegistry\Model\Attribute\Config')
                ->getStaticTypesCodes();
            foreach ($this->getAttributes() as $group) {
                foreach ($group as $code => $attribute) {
                    if (in_array($code, $staticCodes) && !empty($attribute['frontend']['is_listed'])) {
                        $listedAttributes[$code] = $attribute['label'];
                    }
                }
            }
        }
        return $listedAttributes;
    }

    /**
     * Custom handler for giftregistry type save action
     *
     * @param \Magento\Simplexml\Element $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event
     */
    public function postDispatchTypeSave($config, $eventModel, $processor)
    {
        $typeData = \Mage::app()->getRequest()->getParam('type');
        $typeId = isset($typeData['type_id']) ? $typeData['type_id'] : __('New');
        return $eventModel->setInfo($typeId);
    }

    /**
     * Filter and load post data to object
     *
     * @param array $data
     * @return \Magento\GiftRegistry\Model\Type
     */
    public function loadPost(array $data)
    {
        $type = $data['type'];
        $this->setCode($type['code']);

        $attributes = (isset($data['attributes'])) ? $data['attributes'] : null;
        $this->setAttributes($attributes);

        $label = (isset($type['label'])) ? $type['label'] : null;
        $this->setLabel($label);

        $sortOrder = (isset($type['sort_order'])) ? $type['sort_order'] : null;
        $this->setSortOrder($sortOrder);

        $isListed = (isset($type['is_listed'])) ? $type['is_listed'] : null;
        $this->setIsListed($isListed);

        return $this;
    }
}
