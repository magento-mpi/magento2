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
 * Gift Registry helper
 */
class Magento_GiftRegistry_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'magento_giftregistry/general/enabled';
    const XML_PATH_SEND_LIMIT = 'magento_giftregistry/sharing_email/send_limit';
    const XML_PATH_MAX_REGISTRANT   = 'magento_giftregistry/general/max_registrant';

    const ADDRESS_PREFIX = 'gr_address_';

    /**
     * Option for address source selector
     * @var string
     */
    const ADDRESS_NEW = 'new';

    /**
     * Option for address source selector
     * @var string
     */
    const ADDRESS_NONE = 'none';


    /**
     * Check whether gift registry is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Retrieve sharing recipients limit config data
     *
     * @return int
     */
    public function getRecipientsLimit()
    {
        return Mage::getStoreConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Retrieve address prefix
     *
     * @return int
     */
    public function getAddressIdPrefix()
    {
        return self::ADDRESS_PREFIX;
    }

    /**
     * Retrieve Max Recipients
     *
     * @param int $store
     * @return int
     */
    public function getMaxRegistrant($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_REGISTRANT, $store);
    }

    /**
     * Validate custom attributes values
     *
     * @param array $customValues
     * @param array $attributes
     * @return array|bool
     */
    public function validateCustomAttributes($customValues, $attributes)
    {
        $errors = array();
        foreach ($attributes as $field => $data) {
            if (empty($customValues[$field])) {
                if ((!empty($data['frontend'])) && is_array($data['frontend'])
                    && (!empty($data['frontend']['is_required']))) {
                    $errors[] = __('Please enter the "%1".', $data['label']);
                }
            } else {
                if (($data['type']) == 'select' && is_array($data['options'])) {
                    $found = false;
                    foreach ($data['options'] as $option) {
                        if ($customValues[$field] == $option['code']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $errors[] = __('Please enter the correct "%1".', $data['label']);
                    }
                }
            }
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Return list of gift registries
     *
     * @return Magento_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getCurrentCustomerEntityOptions()
    {
        $result = array();
        $entityCollection = Mage::getModel('Magento_GiftRegistry_Model_Entity')->getCollection()
            ->filterByCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
            ->filterByIsActive(1);

        if (count($entityCollection)) {
            foreach ($entityCollection as $entity) {
                $result[] = new Magento_Object(array('value' => $entity->getId(),
                        'title' => $this->escapeHtml($entity->getTitle())));
            }
        }
        return $result;
    }

    /**
     * Format custom dates to internal format
     *
     * @param array|string $data
     * @param array $fieldDateFormats
     *
     * @return array|string
     */
    public function filterDatesByFormat($data, $fieldDateFormats)
    {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as $id => $field) {
            if (!empty($data[$id])) {
                if (!is_array($field)) {
                    if (isset($fieldDateFormats[$id])) {
                        $data[$id] = $this->_filterDate($data[$id], $fieldDateFormats[$id]);
                    }
                } else {
                    foreach ($field as $id2 => $field2) {
                        if (!empty($data[$id][$id2]) && !is_array($field2) && isset($fieldDateFormats[$id2])) {
                            $data[$id][$id2] = $this->_filterDate($data[$id][$id2], $fieldDateFormats[$id2]);
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Convert date in from <$formatIn> to internal format
     *
     * @param   string $value
     * @param   string $formatIn    -  FORMAT_TYPE_FULL, FORMAT_TYPE_LONG, FORMAT_TYPE_MEDIUM, FORMAT_TYPE_SHORT
     * @return  string
     */
    public function _filterDate($value, $formatIn = false)
    {
        if ($formatIn === false) {
            return $value;
        } else {
            $formatIn = Mage::app()->getLocale()->getDateFormat($formatIn);
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $formatIn,
            'locale'      => Mage::app()->getLocale()->getLocaleCode()
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Magento_Date::DATE_INTERNAL_FORMAT
        ));

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * Return frontend registry link
     *
     * @param Magento_GiftRegistry_Model_Entity $entity
     * @return string
     */
    public function getRegistryLink($entity)
    {
        return Mage::getModel('Magento_Core_Model_Url')->setStore($entity->getStoreId())
            ->getUrl('giftregistry/view/index', array('id' => $entity->getUrlKey()));
    }

    /**
     * Check if product can be added to gift registry
     *
     * @param mixed $item
     * @return bool
     */
    public function canAddToGiftRegistry($item)
    {
        if ($item->getIsVirtual()){
            return false;
        }

        if ($item instanceof Magento_Sales_Model_Quote_Item) {
            $productType = $item->getProductType();
        } else {
            $productType = $item->getTypeId();
        }

        if ($productType == Magento_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD) {
            if ($item instanceof Magento_Sales_Model_Quote_Item) {
                $product = Mage::getModel('Magento_Catalog_Model_Product')->load($item->getProductId());
            } else {
                $product = $item;
            }
            return $product->getTypeInstance()->isTypePhysical($product);
        }
        return true;
    }
}
