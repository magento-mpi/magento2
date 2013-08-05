<?php
/**
 * Creates a block given failed registration
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Registration_Failed extends Mage_Backend_Block_Template
{
    /** @var  Mage_Backend_Model_Session */
    protected $_session;

    /**
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Mage_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
