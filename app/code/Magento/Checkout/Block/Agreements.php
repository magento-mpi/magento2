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
     * @var Magento_Checkout_Model_Resource_Agreement_CollectionFactory
     */
    protected $_agreementCollFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Checkout_Model_Resource_Agreement_CollectionFactory $agreementCollFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Checkout_Model_Resource_Agreement_CollectionFactory $agreementCollFactory,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_agreementCollFactory = $agreementCollFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return mixed
     */
    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!$this->_storeConfig->getConfigFlag('checkout/options/enable_agreements')) {
                $agreements = array();
            } else {
                /** @var Magento_Checkout_Model_Resource_Agreement_Collection $agreements */
                $agreements = $this->_agreementCollFactory->create()
                    ->addStoreFilter($this->_storeManager->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
