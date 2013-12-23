<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Customer\Account;

class Link extends \Magento\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationConfiguration;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\Invitation\Model\Config $invitationConfiguration
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\Invitation\Model\Config $invitationConfiguration,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_invitationConfiguration = $invitationConfiguration;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_invitationConfiguration->isEnabledOnFront()) {
            return parent::_toHtml();
        }
        return '';
    }
}
