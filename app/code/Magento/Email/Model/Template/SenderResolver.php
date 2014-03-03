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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
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
            $result['name'] = $this->_storeConfig->getConfig('trans_email/ident_' . $sender . '/name', $scopeId);
            $result['email'] = $this->_storeConfig->getConfig('trans_email/ident_' . $sender . '/email', $scopeId);
        } else {
            $result = $sender;
        }

        if (!isset($result['name']) || !isset($result['email'])) {
            throw new \Magento\Mail\Exception(__('Invalid sender data'));
        }

        return $result;
    }
}
