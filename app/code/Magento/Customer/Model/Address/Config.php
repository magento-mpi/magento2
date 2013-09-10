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
class Magento_Customer_Model_Address_Config extends Magento_Core_Model_Config_Base
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
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
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
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Define node
     *
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
        parent::__construct($this->_coreConfig->getNode()->global->customer->address);
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
            foreach ($this->getNode('formats')->children() as $typeCode => $typeConfig) {
                $path = sprintf('%s%s', self::XML_PATH_ADDRESS_TEMPLATE, $typeCode);
                $type = new Magento_Object();
                $escapeHtml = strtolower($typeConfig->escapeHtml);
                $escapeHtml = $escapeHtml == 'false' || $escapeHtml == '0' || $escapeHtml == 'no'
                        || !strlen($typeConfig->escapeHtml) ? false : true;
                $type->setCode($typeCode)
                    ->setTitle((string)$typeConfig->title)
                    ->setDefaultFormat($this->_coreStoreConfig->getConfig($path, $store))
                    ->setEscapeHtml($escapeHtml);

                $renderer = (string)$typeConfig->renderer;
                if (!$renderer) {
                    $renderer = self::DEFAULT_ADDRESS_RENDERER;
                }

                $type->setRenderer(
                    Mage::helper('Magento_Customer_Helper_Address')->getRenderer($renderer)->setType($type)
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
        if(!isset($this->_defaultType[$storeId])) {
            $this->_defaultType[$storeId] = new Magento_Object();
            $this->_defaultType[$storeId]->setCode('default')
                ->setDefaultFormat('{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}'
                        . '{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, '
                        . '{{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');

            $this->_defaultType[$storeId]->setRenderer(
                Mage::helper('Magento_Customer_Helper_Address')
                    ->getRenderer(self::DEFAULT_ADDRESS_RENDERER)->setType($this->_defaultType[$storeId])
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
        foreach($this->getFormats() as $type) {
            if($type->getCode()==$typeCode) {
                return $type;
            }
        }
        return $this->_getDefaultFormat();
    }

}
