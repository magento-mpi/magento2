<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_Entity
{
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
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator,
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     * @param Saas_Limitation_Model_Dictionary $dictionary
     * @param string $messageCode
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Limitation_LimitationInterface $limitation,
        Saas_Limitation_Model_Dictionary $dictionary,
        $messageCode
    ) {
        $this->_limitationValidator = $limitationValidator;
        $this->_limitation = $limitation;
        $this->_message = $dictionary->getMessage($messageCode);
    }

    /**
     * Restrict creation of new entities upon reaching the limitation
     *
     * @param Magento_Event_Observer $observer
     * @throws Magento_Core_Exception
     */
    public function restrictCreation(Magento_Event_Observer $observer)
    {
        /** @var Magento_Core_Model_Abstract $model */
        $model = $observer->getEvent()->getData('data_object');
        if ($model->isObjectNew() && $this->_limitationValidator->exceedsThreshold($this->_limitation)) {
            $exception = new Magento_Core_Exception($this->_message);
            $exception->addMessage(new Magento_Core_Model_Message_Error($this->_message));
            throw $exception;
        }
    }
}
