<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer address config
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Address_Config extends Magento_Config_Data
{
    const DEFAULT_ADDRESS_RENDERER  = 'Magento_Customer_Block_Address_Renderer_Default';
    const XML_PATH_ADDRESS_TEMPLATE = 'customer/address_templates/';
    const DEFAULT_ADDRESS_FORMAT    = 'oneline';

    /**
     * Customer Address Templates per store
     *
     * @var array
     */
    protected $_types           = array();

    /**
     * Current store instance
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store           = null;

    /**
     * Default types per store
     * Using for invalid code
     *
     * @var array
     */
    protected $_defaultTypes    = array();

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Helper_Address
     */
    protected $_addressHelper;

    /**
     * @param Magento_Customer_Model_Address_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Customer_Helper_Address $addressHelper
     * @param string $cacheId
     */
    public function __construct(
        Magento_Customer_Model_Address_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Customer_Helper_Address $addressHelper,
        $cacheId = 'address_format'
    ) {
        parent::__construct($reader, $cache, $cacheId);
        $this->_storeManager = $storeManager;
        $this->_addressHelper = $addressHelper;
    }

    /**
     * Set store
     *
     * @param null|string|bool|int|Magento_Core_Model_Store $store
     * @return Magento_Customer_Model_Address_Config
     */
    public function setStore($store)
    {
        $this->_store = $this->_storeManager->getStore($store);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = $this->_storeManager->getStore();
        }
        return $this->_store;
    }

    /**
     * Retrieve address formats
     *
     * @return array
     */
    public function getFormats()
    {
        $store = $this->getStore();
        $storeId = $store->getId();
        if (!isset($this->_types[$storeId])) {
            $this->_types[$storeId] = array();
            foreach ($this->get() as $typeCode => $typeConfig) {
                $path = sprintf('%s%s', self::XML_PATH_ADDRESS_TEMPLATE, $typeCode);
                $type = new Magento_Object();
                if (isset($typeConfig['escapeHtml'])
                    && ($typeConfig['escapeHtml'] == 'true' || $typeConfig['escapeHtml'] == '1')
                ) {
                    $escapeHtml = true;
                } else {
                    $escapeHtml = false;
                }

                $type->setCode($typeCode)
                    ->setTitle((string)$typeConfig['title'])
                    ->setDefaultFormat($store->getConfig($path))
                    ->setEscapeHtml($escapeHtml);

                $renderer = isset($typeConfig['renderer']) ? (string)$typeConfig['renderer'] : null;
                if (!$renderer) {
                    $renderer = self::DEFAULT_ADDRESS_RENDERER;
                }

                $type->setRenderer(
                    $this->_addressHelper->getRenderer($renderer)->setType($type)
                );

                $this->_types[$storeId][] = $type;
            }
        }

        return $this->_types[$storeId];
    }

    /**
     * Retrieve default address format
     *
     * @return Magento_Object
     */
    protected function _getDefaultFormat()
    {
        $store = $this->getStore();
        $storeId = $store->getId();
        if (!isset($this->_defaultType[$storeId])) {
            $this->_defaultType[$storeId] = new Magento_Object();
            $this->_defaultType[$storeId]->setCode('default')
                ->setDefaultFormat('{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}'
                        . '{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, '
                        . '{{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');

            $this->_defaultType[$storeId]->setRenderer(
                $this->_addressHelper
                    ->getRenderer(self::DEFAULT_ADDRESS_RENDERER)
                    ->setType($this->_defaultType[$storeId])
            );
        }
        return $this->_defaultType[$storeId];
    }

    /**
     * Retrieve address format by code
     *
     * @param string $typeCode
     * @return Magento_Object
     */
    public function getFormatByCode($typeCode)
    {
        foreach ($this->getFormats() as $type) {
            if ($type->getCode() == $typeCode) {
                return $type;
            }
        }
        return $this->_getDefaultFormat();
    }

}
