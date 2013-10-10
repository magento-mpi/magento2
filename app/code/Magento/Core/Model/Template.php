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
namespace Magento\Core\Model;

abstract class Template extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\Object
     */
    protected $_designConfig;


    /**
     * Configuration of emulated desing package.
     *
     * @var \Magento\Object|boolean
     */
    protected $_emulatedDesignConfig = false;

    /**
     * Initial environment information
     * @see self::_applyDesignConfig()
     *
     * @var \Magento\Object|null
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
     * Design package instance
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @var \Magento\Core\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\App\Emulation $appEmulation
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\App\Emulation $appEmulation,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_design = $design;
        $this->_area = isset($data['area']) ? $data['area'] : null;
        $this->_store = isset($data['store']) ? $data['store'] : null;
        $this->_appEmulation = $appEmulation;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * Applying of design config
     *
     * @return \Magento\Core\Model\Template
     */
    protected function _applyDesignConfig()
    {
        $designConfig = $this->getDesignConfig();
        $store = $designConfig->getStore();
        $storeId = is_object($store) ? $store->getId() : $store;
        $area = $designConfig->getArea();
        if (!is_null($storeId)) {
            $this->_initialEnvironmentInfo = $this->_appEmulation->startEnvironmentEmulation($storeId, $area);
        }
        return $this;
    }

    /**
     * Revert design settings to previous
     *
     * @return \Magento\Core\Model\Template
     */
    protected function _cancelDesignConfig()
    {
        if (!empty($this->_initialEnvironmentInfo)) {
            $this->_appEmulation->stopEnvironmentEmulation($this->_initialEnvironmentInfo);
            $this->_initialEnvironmentInfo = null;
        }
        return $this;
    }

    /**
     * Get design configuration data
     *
     * @return \Magento\Object
     */
    public function getDesignConfig()
    {
        if ($this->_designConfig === null) {
            if ($this->_area === null) {
                $this->_area = $this->_design->getArea();
            }
            if ($this->_store === null) {
                $this->_store = $this->_storeManager->getStore()->getId();
            }
            $this->_designConfig = new \Magento\Object(array(
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
     * @return \Magento\Core\Model\Template
     * @throws \Magento\Exception
     */
    public function setDesignConfig(array $config)
    {
        if (!isset($config['area']) || !isset($config['store'])) {
            throw new \Magento\Exception('Design config must have area and store.');
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
