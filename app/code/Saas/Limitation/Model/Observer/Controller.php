<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_Controller
{
    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Limitation_Validator
     */
    private $_limitationValidator;

    /**
     * @var Saas_Limitation_Model_Limitation_LimitationInterface
     */
    private $_limitation;

    /**
     * @var string
     */
    private $_message;

    /**
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     * @param Saas_Limitation_Model_Dictionary $dictionary
     * @param string $messageCode
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Limitation_LimitationInterface $limitation,
        Saas_Limitation_Model_Dictionary $dictionary,
        $messageCode
    ) {
        $this->_session = $session;
        $this->_limitationValidator = $limitationValidator;
        $this->_limitation = $limitation;
        $this->_message = $dictionary->getMessage($messageCode);
    }

    /**
     * Display message in the notification area upon reaching the limitation
     *
     * @param Magento_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function displayNotification(Magento_Event_Observer $observer)
    {
        if ($this->_limitationValidator->exceedsThreshold($this->_limitation)) {
            $this->_session->addNotice($this->_message);
        }
    }
}
