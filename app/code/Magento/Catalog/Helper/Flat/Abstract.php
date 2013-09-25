<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog flat abstract helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Helper_Flat_Abstract extends Magento_Core_Helper_Abstract
{
    /**
     * Catalog Flat index process code
     *
     * @var null|string
     */
    protected $_indexerCode = null;

    /**
     * Store catalog Flat index process instance
     *
     * @var Magento_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Check if Catalog Flat Data has been initialized
     *
     * @return bool
     */
    abstract public function isBuilt();

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param mixed $deprecatedParam this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    abstract public function isEnabled($deprecatedParam = false);

    /**
     * Process factory
     *
     * @var Magento_Index_Model_ProcessFactory
     */
    protected $_processFactory;

    /**
     * Construct
     *
     * @param Magento_Index_Model_ProcessFactory $processFactory
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Index_Model_ProcessFactory $processFactory,
        Magento_Core_Helper_Context $context
    ) {
        $this->_processFactory = $processFactory;
        parent::__construct($context);
    }

    /**
     * Check if Catalog Category Flat Data is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isEnabled() && !$this->getProcess()->isLocked()
            && $this->getProcess()->getStatus() != Magento_Index_Model_Process::STATUS_RUNNING;
    }

    /**
     * Retrieve Catalog Flat index process
     *
     * @return Magento_Index_Model_Process
     */
    public function getProcess()
    {
        if (is_null($this->_process)) {
            $this->_process = $this->_processFactory->create()
                ->load($this->_indexerCode, 'indexer_code');
        }
        return $this->_process;
    }
}
