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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($sender, $scopeId = null)
    {
        $result = array();

        if (!is_array($sender)) {
            $result['name'] = $this->_scopeConfig->getValue(
                'trans_email/ident_' . $sender . '/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $scopeId
            );
            $result['email'] = $this->_scopeConfig->getValue(
                'trans_email/ident_' . $sender . '/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $scopeId
            );
        } else {
            $result = $sender;
        }

        if (!isset($result['name']) || !isset($result['email'])) {
            throw new \Magento\Mail\Exception(__('Invalid sender data'));
        }

        return $result;
    }
}
