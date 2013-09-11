<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout api resource
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Api;

class Resource extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Attributes map array per entity type
     *
     * @var array
     */
    protected $_attributesMap = array(
        'global' => array(),
    );

    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'global' => array('entity_id', 'attribute_set_id', 'entity_type_id')
    );

    /**
     * Field name in session for saving store id
     *
     * @var string
     */
    protected $_storeIdSessionField = 'store_id';

    /** @var \Magento\Api\Helper\Data */
    protected $_apiHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Api\Helper\Data $apiHelper
     */
    public function __construct(\Magento\Api\Helper\Data $apiHelper)
    {
        $this->_apiHelper = $apiHelper;
    }

    /**
     * Check if quote already exist with provided quoteId for creating
     *
     * @param int $quoteId
     * @return bool
     */
    protected function _isQuoteExist($quoteId)
    {
        if (empty($quoteId)) {
            return false;
        }

        try {
            $quote = $this->_getQuote($quoteId);
        } catch (\Magento\Api\Exception $e) {
            return false;
        }

        if (!is_null($quote->getId())) {
            $this->_fault('quote_already_exist');
        }

        return false;
    }

    /**
     * Retrieves store id from store code, if no store id specified,
     * it use set session or admin store
     *
     * @param string|int $store
     * @return int
     */
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = \Mage::app()->getStore($store)->getId();

        } catch (\Magento\Core\Model\Store\Exception $e) {
            $this->_fault('store_not_exists');
        }

        return $storeId;
    }

    /**
     * Retrieves quote by quote identifier and store code or by quote identifier
     *
     * @param int $quoteId
     * @param string|int $store
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote($quoteId, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Mage::getModel('Magento\Sales\Model\Quote');

        if (!(is_string($store) || is_integer($store))) {
            $quote->loadByIdWithoutStore($quoteId);
        } else {
            $storeId = $this->_getStoreId($store);

            $quote->setStoreId($storeId)
                ->load($quoteId);
        }
        if (is_null($quote->getId())) {
            $this->_fault('quote_not_exists');
        }

        return $quote;
    }

    /**
     * Get store identifier by quote identifier
     *
     * @param int $quoteId
     * @return int
     */
    protected function _getStoreIdFromQuote($quoteId)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Mage::getModel('Magento\Sales\Model\Quote')
            ->loadByIdWithoutStore($quoteId);

        return $quote->getStoreId();
    }

    /**
     * Update attributes for entity
     *
     * @param array $data
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $type
     * @param array|null $attributes
     * @return \Magento\Checkout\Model\Api\Resource
     */
    protected function _updateAttributes($data, $object, $type, array $attributes = null)
    {
        foreach ($data as $attribute => $value) {
            if ($this->_apiHelper->isAttributeAllowed($attribute, $type, $this->_ignoredAttributeCodes, $attributes)) {
                $object->setData($attribute, $value);
            }
        }

        return $this;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $type
     * @param array $attributes
     * @return \Magento\Checkout\Model\Api\Resource
     */
    protected function _getAttributes($object, $type, array $attributes = null)
    {
        $result = array();
        if (!is_object($object)) {
            return $result;
        }
        foreach ($object->getData() as $attribute => $value) {
            if (is_object($value)) {
                continue;
            }

            if ($this->_apiHelper->isAttributeAllowed($attribute, $type, $this->_ignoredAttributeCodes, $attributes)) {
                $result[$attribute] = $value;
            }
        }
        if (isset($this->_attributesMap[$type])) {
            foreach ($this->_attributesMap[$type] as $alias => $attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }
        foreach ($this->_attributesMap['global'] as $alias => $attributeCode) {
            $result[$alias] = $object->getData($attributeCode);
        }
        return $result;
    }
}
