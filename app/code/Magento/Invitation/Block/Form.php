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
     * Returns maximal number of invitations to send in one try
     *
     * @return int
     */
    public function getMaxInvitationsPerSend()
    {
        return \Mage::getSingleton('Magento\Invitation\Model\Config')->getMaxInvitationsPerSend();
    }

    /**
     * Returns whether custom invitation message allowed
     *
     * @return bool
     */
    public function isInvitationMessageAllowed()
    {
        return \Mage::getSingleton('Magento\Invitation\Model\Config')->isInvitationMessageAllowed();
    }
}
