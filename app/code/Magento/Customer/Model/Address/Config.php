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
namespace Magento\Customer\Model\Address;

class Config extends \Magento\Core\Model\Config\Base
{
    const DEFAULT_ADDRESS_RENDERER  = 'Magento\Customer\Block\Address\Renderer\DefaultRenderer';
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
     * @var \Magento\Core\Model\Store
     */
    protected $_store           = null;

    /**
     * Default types per store
     * Using for invalid code
     *
     * @var array
     */
    protected $_defaultTypes    = array();

    public function setStore($store)
    {
        $this->_store = \Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = \Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Define node
     *
     */
    public function __construct()
    {
        parent::__construct(\Mage::getConfig()->getNode()->global->customer->address);
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
                $type = new \Magento\Object();
                $escapeHtml = strtolower($typeConfig->escapeHtml);
                $escapeHtml = $escapeHtml == 'false' || $escapeHtml == '0' || $escapeHtml == 'no'
                        || !strlen($typeConfig->escapeHtml) ? false : true;
                $type->setCode($typeCode)
                    ->setTitle((string)$typeConfig->title)
                    ->setDefaultFormat(\Mage::getStoreConfig($path, $store))
                    ->setEscapeHtml($escapeHtml);

                $renderer = (string)$typeConfig->renderer;
                if (!$renderer) {
                    $renderer = self::DEFAULT_ADDRESS_RENDERER;
                }

                $type->setRenderer(
                    \Mage::helper('Magento\Customer\Helper\Address')->getRenderer($renderer)->setType($type)
                );

                $this->_types[$storeId][] = $type;
            }
        }

        return $this->_types[$storeId];
    }

    /**
     * Retrieve default address format
     *
     * @return \Magento\Object
     */
    protected function _getDefaultFormat()
    {
        $store = $this->getStore();
        $storeId = $store->getId();
        if(!isset($this->_defaultType[$storeId])) {
            $this->_defaultType[$storeId] = new \Magento\Object();
            $this->_defaultType[$storeId]->setCode('default')
                ->setDefaultFormat('{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}'
                        . '{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, '
                        . '{{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');

            $this->_defaultType[$storeId]->setRenderer(
                \Mage::helper('Magento\Customer\Helper\Address')
                    ->getRenderer(self::DEFAULT_ADDRESS_RENDERER)->setType($this->_defaultType[$storeId])
            );
        }
        return $this->_defaultType[$storeId];
    }

    /**
     * Retrieve address format by code
     *
     * @param string $typeCode
     * @return \Magento\Object
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
