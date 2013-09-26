<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Return_Tracking extends Magento_Core_Block_Template
{
    /**
     * Get whether rma is allowed for PSL
     *
     * @var bool|null
     */
    protected $_isRmaAvailableForPrintLabel;

    protected $_template = 'return/tracking.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_coreSession;

    /**
     * @param Magento_Core_Model_Session $coreSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session $coreSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreSession = $coreSession;
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setRma($this->_coreRegistry->registry('current_rma'));
    }

    /**
     * Get collection of tracking numbers of RMA
     *
     * @return Magento_Rma_Model_Resource_Shipping_Collection|array
     */
    public function getTrackingNumbers()
    {
        if ($this->getRma()) {
            return $this->getRma()->getTrackingNumbers();
        }
        return array();
    }

    /**
     * Get url for delete label action
     *
     * @return string
     */
    public function getDeleteLabelUrl()
    {
        if ($this->getRma()) {
            return $this->getUrl('*/*/delLabel/', array('entity_id' => $this->getRma()->getEntityId()));
        }
        return '';
    }

    /**
     * Get messages on AJAX errors
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $message = $this->_coreSession->getErrorMessage();
        $this->_coreSession->unsErrorMessage();
        return $message;
    }

    /**
     * Get whether rma is allowed for PSL
     *
     * @return bool
     */
    public function isPrintShippingLabelAllowed()
    {
        if ($this->_isRmaAvailableForPrintLabel === null) {
            $this->_isRmaAvailableForPrintLabel = $this->getRma() && $this->getRma()->isAvailableForPrintLabel();
        }
        return $this->_isRmaAvailableForPrintLabel;
    }
}
