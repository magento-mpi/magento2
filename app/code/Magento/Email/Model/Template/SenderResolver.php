<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Email\Model\Template;

class SenderResolver implements \Magento\Mail\Template\SenderResolverInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Store\Model\ConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\Store\Model\ConfigInterface $coreStoreConfig
    ) {
        $this->_storeConfig = $coreStoreConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($sender, $scopeId = null)
    {
        $result = array();

        if (!is_array($sender)) {
            $result['name'] = $this->_storeConfig->getValue('trans_email/ident_' . $sender . '/name', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $scopeId);
            $result['email'] = $this->_storeConfig->getValue('trans_email/ident_' . $sender . '/email', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $scopeId);
        } else {
            $result = $sender;
        }

        if (!isset($result['name']) || !isset($result['email'])) {
            throw new \Magento\Mail\Exception(__('Invalid sender data'));
        }

        return $result;
    }
}
