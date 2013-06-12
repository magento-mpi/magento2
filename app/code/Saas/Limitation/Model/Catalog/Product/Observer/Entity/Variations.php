<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Entity_Variations
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
     * Restrict creation of new entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Catalog_Exception
     */
    public function restrictCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $entity */
        $entity = $observer->getEvent()->getData('product');
        $variations = $observer->getEvent()->getData('variations');
        $qty = ($entity->isObjectNew() ? 1 : 0) + count($variations);
        if ($qty > 0 && $this->_limitationValidator->isThresholdReached($this->_limitation, $qty)) {
            $message = sprintf($this->_message, $qty, $this->_limitation->getThreshold());
            throw new Mage_Catalog_Exception($message);
        }
    }
}
