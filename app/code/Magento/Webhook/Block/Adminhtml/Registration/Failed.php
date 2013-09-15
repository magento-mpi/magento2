<?php
/**
 * Creates a block given failed registration
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

class Failed extends \Magento\Backend\Block\Template
{
    /** @var  \Magento\Backend\Model\Session */
    protected $_session;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Model_Session $session
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_session = $session;
    }

    /**
     * Get error message produced on failure
     *
     * @return string The error message produced upon failure
     */
    public function getSessionError()
    {
        return $this->_session->getMessages(true)
            ->getLastAddedMessage()
            ->toString();
    }
}
