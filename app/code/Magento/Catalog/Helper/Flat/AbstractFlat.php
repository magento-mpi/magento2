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
namespace Magento\Catalog\Helper\Flat;

abstract class AbstractFlat extends \Magento\App\Helper\AbstractHelper
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
     * @var \Magento\Index\Model\Process|null
     */
    protected $_process = null;

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @return bool
     */
    abstract public function isEnabled();

    /**
     * Process factory
     *
     * @var \Magento\Index\Model\ProcessFactory
     */
    protected $_processFactory;

    /**
     * @var bool
     */
    protected $_isAvailable;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Index\Model\ProcessFactory $processFactory
     * @param bool $isAvailable
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Index\Model\ProcessFactory $processFactory,
        $isAvailable = true
    ) {
        $this->_processFactory = $processFactory;
        $this->_isAvailable = $isAvailable;
        parent::__construct($context);
    }

    /**
     * Check if Catalog Category Flat Data is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->_isAvailable
            && $this->isEnabled()
            && !$this->getProcess()->isLocked()
            && $this->getProcess()->getStatus() != \Magento\Index\Model\Process::STATUS_RUNNING;
    }

    /**
     * Retrieve Catalog Flat index process
     *
     * @return \Magento\Index\Model\Process
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
