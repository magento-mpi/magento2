<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Entity_Duplication
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
     * Restrict operation on an entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function restrict(Varien_Event_Observer $observer)
    {
        if ($this->_limitationValidator->isThresholdReached($this->_limitation)) {
            $exception = new Mage_Core_Exception($this->_message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($this->_message));
            throw $exception;
        }
    }
}
