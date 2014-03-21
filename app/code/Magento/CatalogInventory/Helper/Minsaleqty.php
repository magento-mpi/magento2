<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * MinSaleQty value manipulation helper
 */
namespace Magento\CatalogInventory\Helper;

use Magento\Store\Model\Store;

class Minsaleqty
{
    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Math\Random $mathRandom
    ) {
        $this->_storeConfig = $coreStoreConfig;
        $this->mathRandom = $mathRandom;
    }

    /**
     * Retrieve fixed qty value
     *
     * @param int|float|string|null $qty
     * @return float|null
     */
    protected function _fixQty($qty)
    {
        return (!empty($qty) ? (float)$qty : null);
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function _serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float)$value;
            return (string)$data;
        } else if (is_array($value)) {
            $data = array();
            foreach ($value as $groupId => $qty) {
                if (!array_key_exists($groupId, $data)) {
                    $data[$groupId] = $this->_fixQty($qty);
                }
            }
            if (count($data) == 1 && array_key_exists(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $data)) {
                return (string)$data[\Magento\Customer\Model\Group::CUST_GROUP_ALL];
            }
            return serialize($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function _unserializeValue($value)
    {
        if (is_numeric($value)) {
            return array(
                \Magento\Customer\Model\Group::CUST_GROUP_ALL => $this->_fixQty($value)
            );
        } else if (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return array();
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function _isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('customer_group_id', $row) || !array_key_exists('min_sale_qty', $row)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function _encodeArrayFieldValue(array $value)
    {
        $result = array();
        foreach ($value as $groupId => $qty) {
            $_id = $this->mathRandom->getUniqueHash('_');
            $result[$_id] = array(
                'customer_group_id' => $groupId,
                'min_sale_qty' => $this->_fixQty($qty),
            );
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function _decodeArrayFieldValue(array $value)
    {
        $result = array();
        unset($value['__empty']);
        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('customer_group_id', $row) || !array_key_exists('min_sale_qty', $row)) {
                continue;
            }
            $groupId = $row['customer_group_id'];
            $qty = $this->_fixQty($row['min_sale_qty']);
            $result[$groupId] = $qty;
        }
        return $result;
    }

    /**
     * Retrieve min_sale_qty value from config
     *
     * @param int $customerGroupId
     * @param null|string|bool|int|Store $store
     * @return float|null
     */
    public function getConfigValue($customerGroupId, $store = null)
    {
        $value = $this->_storeConfig->getValue(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_MIN_SALE_QTY, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        $value = $this->_unserializeValue($value);
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $groupId => $qty) {
            if ($groupId == $customerGroupId) {
                $result = $qty;
                break;
            } else if ($groupId == \Magento\Customer\Model\Group::CUST_GROUP_ALL) {
                $result = $qty;
            }
        }
        return $this->_fixQty($result);
    }

    /**
     * Make value readable by \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->_unserializeValue($value);
        if (!$this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }
        $value = $this->_serializeValue($value);
        return $value;
    }
}
