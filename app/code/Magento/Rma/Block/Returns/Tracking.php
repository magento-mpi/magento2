<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Returns;

class Tracking extends \Magento\Core\Block\Template
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
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
     * @return \Magento\Rma\Model\Resource\Shipping\Collection|array
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
        $message = \Mage::getSingleton('Magento\Core\Model\Session')->getErrorMessage();
        \Mage::getSingleton('Magento\Core\Model\Session')->unsErrorMessage();
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
