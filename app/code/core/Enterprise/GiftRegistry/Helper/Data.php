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
 * Gift Registry helper
 */
class Enterprise_GiftRegistry_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    const XML_PATH_ENABLED = 'enterprise_giftregistry/general/enabled';
    const XML_PATH_SEND_LIMIT = 'enterprise_giftregistry/sharing_email/send_limit';
    const XML_PATH_MAX_REGISTRANT   = 'enterprise_giftregistry/general/max_registrant';

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
     * Check whether reminder rules should be enabled
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
                    $errors[] = $this->__('Please enter the "%s".', $data['label']);
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
                        $errors[] = $this->__('Please enter correct "%s".', $data['label']);
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
            'date_format' => $formatIn
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);

        return $value;
    }

}
