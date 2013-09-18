<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Model\System\Config\Backend;

class Pool extends \Magento\Core\Model\Config\Value
{
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
            \Mage::getModel('Magento\GiftCardAccount\Model\Pool')->cleanupFree();
        }
        parent::_afterSave();
    }

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
                $sep = \Mage::getModel('Magento\GiftCardAccount\Model\Pool')->getCodeSeparator();
                $len += (ceil($codeLen / $v) * strlen($sep)) - 1;
            }
        }

        if ($len > 255) {
            \Mage::throwException(__('Maximum generated code length is 255. Please correct your settings.'));
        }
    }
}
