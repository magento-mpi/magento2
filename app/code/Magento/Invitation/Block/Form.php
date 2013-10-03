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
 * Front end helper block to render form
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block;

class Form extends \Magento\Core\Block\Template
{
    /**
     * Invitation Config
     * 
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Invitation\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData, 
        \Magento\Core\Block\Template\Context $context,
        \Magento\Invitation\Model\Config $config,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_config = $config;
    }

    /**
     * Returns maximal number of invitations to send in one try
     *
     * @return int
     */
    public function getMaxInvitationsPerSend()
    {
        return $this->_config->getMaxInvitationsPerSend();
    }

    /**
     * Returns whether custom invitation message allowed
     *
     * @return bool
     */
    public function isInvitationMessageAllowed()
    {
        return $this->_config->isInvitationMessageAllowed();
    }
}
