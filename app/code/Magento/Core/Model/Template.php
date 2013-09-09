<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Template model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Core_Model_Template extends Magento_Core_Model_Abstract
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    /**
     * Default design area for emulation
     */
    const DEFAULT_DESIGN_AREA = 'frontend';

    /**
     * Configuration of desing package for template
     *
     * @var Magento_Object
     */
    protected $_designConfig;


    /**
     * Configuration of emulated desing package.
     *
     * @var Magento_Object|boolean
     */
    protected $_emulatedDesignConfig = false;

    /**
     * Initial environment information
     * @see self::_applyDesignConfig()
     *
     * @var Magento_Object|null
     */
    protected $_initialEnvironmentInfo = null;

    /**
     * Package area
     *
     * @var string
     */
    protected $_area;

    /**
     * Store id
     *
     * @var int
     */
    protected $_store;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_area = isset($data['area']) ? $data['area'] : null;
        $this->_store = isset($data['store']) ? $data['store'] : null;
        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * Applying of design config
     *
     * @return Magento_Core_Model_Template
     */
    protected function _applyDesignConfig()
    {
        $designConfig = $this->getDesignConfig();
        $store = $designConfig->getStore();
        $storeId = is_object($store) ? $store->getId() : $store;
        $area = $designConfig->getArea();
        if (!is_null($storeId)) {
            /** @var $appEmulation Magento_Core_Model_App_Emulation */
            $appEmulation = Mage::getSingleton('Magento_Core_Model_App_Emulation');
            $this->_initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, $area);
        }
        return $this;
    }

    /**
     * Revert design settings to previous
     *
     * @return Magento_Core_Model_Template
     */
    protected function _cancelDesignConfig()
    {
        if (!empty($this->_initialEnvironmentInfo)) {
            $appEmulation = Mage::getSingleton('Magento_Core_Model_App_Emulation');
            $appEmulation->stopEnvironmentEmulation($this->_initialEnvironmentInfo);
            $this->_initialEnvironmentInfo = null;
        }
        return $this;
    }

    /**
     * Get design configuration data
     *
     * @return Magento_Object
     */
    public function getDesignConfig()
    {
        if ($this->_designConfig === null) {
            if ($this->_area === null) {
                $this->_area = Mage::getDesign()->getArea();
            }
            if ($this->_store === null) {
                $this->_store = Mage::app()->getStore()->getId();
            }
            $this->_designConfig = new Magento_Object(array(
                'area' => $this->_area,
                'store' => $this->_store
            ));
        }
        return $this->_designConfig;
    }

    /**
     * Initialize design information for template processing
     *
     * @param array $config
     * @return Magento_Core_Model_Template
     * @throws Magento_Exception
     */
    public function setDesignConfig(array $config)
    {
        if (!isset($config['area']) || !isset($config['store'])) {
            throw new Magento_Exception('Design config must have area and store.');
        }
        $this->getDesignConfig()->setData($config);
        return $this;
    }

    /**
     * Save current design config and replace with design config from specified store
     * Event is not dispatched.
     *
     * @param int|string $storeId
     * @param string $area
     */
    public function emulateDesign($storeId, $area=self::DEFAULT_DESIGN_AREA)
    {
        if ($storeId) {
            // save current design settings
            $this->_emulatedDesignConfig = clone $this->getDesignConfig();
            if ($this->getDesignConfig()->getStore() != $storeId) {
                $this->setDesignConfig(array('area' => $area, 'store' => $storeId));
                $this->_applyDesignConfig();
            }
        } else {
            $this->_emulatedDesignConfig = false;
        }
    }

    /**
     * Revert to last design config, used before emulation
     *
     */
    public function revertDesign()
    {
        if ($this->_emulatedDesignConfig) {
            $this->setDesignConfig($this->_emulatedDesignConfig->getData());
            $this->_cancelDesignConfig();
            $this->_emulatedDesignConfig = false;
        }
    }

    /**
     * Return true if template type eq text
     *
     * @return boolean
     */
    public function isPlain()
    {
        return $this->getType() == self::TYPE_TEXT;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    abstract public function getType();
}
