<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_System_Config_Backend_Pool extends Magento_Core_Model_Config_Value
{
    /**
     * Gift card account pool
     *
     * @var Magento_GiftCardAccount_Model_Pool
     */
    protected $_giftCardAccountPool = null;

    /**
     * @param Magento_GiftCardAccount_Model_Pool $giftCardAccountPool
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_GiftCardAccount_Model_Pool $giftCardAccountPool,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_giftCardAccountPool = $giftCardAccountPool;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    protected function _beforeSave()
    {
        if ($this->isValueChanged()) {
            if (!$this->_coreRegistry->registry('giftcardaccount_code_length_check')) {
                $this->_coreRegistry->register('giftcardaccount_code_length_check', 1);
                $this->_checkMaxLength();
            }
        }
        parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_giftCardAccountPool->cleanupFree();
        }
        parent::_afterSave();
    }

    /**
     * Check Max Length
     *
     * @throws Magento_Core_Exception
     */
    protected function _checkMaxLength()
    {
        $groups = $this->getGroups();
        if (isset($groups['general']['fields'])) {
            $fields = $groups['general']['fields'];
        }

        $len = 0;
        $codeLen = 0;
        if (isset($fields['code_length']['value'])) {
            $codeLen = (int) $fields['code_length']['value'];
            $len += $codeLen;
        }
        if (isset($fields['code_suffix']['value'])) {
            $len += strlen($fields['code_suffix']['value']);
        }
        if (isset($fields['code_prefix']['value'])) {
            $len += strlen($fields['code_prefix']['value']);
        }
        if (isset($fields['code_split']['value'])) {
            $v = (int)$fields['code_split']['value'];
            if ($v > 0 && $v < $codeLen) {
                $sep = $this->_giftCardAccountPool->getCodeSeparator();
                $len += (ceil($codeLen / $v) * strlen($sep)) - 1;
            }
        }

        if ($len > 255) {
            throw new Magento_Core_Exception(__('Maximum generated code length is 255. Please correct your settings.'));
        }
    }
}
