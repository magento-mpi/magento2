<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Controller_Redirect
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
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
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
     * Restrict redirect to new product creation page, if the limitation is reached
     *
     * @param Magento_Event_Observer $observer
     * @throws Mage_Catalog_Exception
     */
    public function restrictNewEntityCreation(Magento_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Controller_Catalog_Product $controller */
        $controller = $observer->getEvent()->getData('controller');
        $redirectTarget = $controller->getRequest()->getParam('back');
        if ($redirectTarget == 'new' && $this->_limitationValidator->exceedsThreshold($this->_limitation)) {
            throw new Mage_Catalog_Exception($this->_message);
        }
    }
}
