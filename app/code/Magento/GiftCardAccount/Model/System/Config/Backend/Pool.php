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
    /**
     * Gift card account pool
     *
     * @var \Magento\GiftCardAccount\Model\Pool
     */
    protected $_giftCardAccountPool = null;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\GiftCardAccount\Model\Pool $giftCardAccountPool
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\GiftCardAccount\Model\Pool $giftCardAccountPool,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_giftCardAccountPool = $giftCardAccountPool;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->isValueChanged()) {
            if (!$this->_registry->registry('giftcardaccount_code_length_check')) {
                $this->_registry->register('giftcardaccount_code_length_check', 1);
                $this->_checkMaxLength();
            }
        }
        parent::_beforeSave();
    }

    /**
     * @return $this
     */
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
     * @return void
     * @throws \Magento\Model\Exception
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
            $codeLen = (int)$fields['code_length']['value'];
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
                $len += ceil($codeLen / $v) * strlen($sep) - 1;
            }
        }

        if ($len > 255) {
            throw new \Magento\Model\Exception(
                __('Maximum generated code length is 255. Please correct your settings.')
            );
        }
    }
}
