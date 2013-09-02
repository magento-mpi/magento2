<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Agreements extends Magento_Core_Block_Template
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $data);
    }

    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!$this->_coreStoreConfig->getConfigFlag('checkout/options/enable_agreements')) {
                $agreements = array();
            } else {
                $agreements = Mage::getModel('Magento_Checkout_Model_Agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
