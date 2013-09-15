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
namespace Magento\Invitation\Block;

class Link extends \Magento\Core\Block\Template
{
    /**
     * Invitation data
     *
     * @var Magento_Invitation_Helper_Data
     */
    protected $_invitationData = null;

    /**
     * @param Magento_Invitation_Helper_Data $invitationData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Invitation_Helper_Data $invitationData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_invitationData = $invitationData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding link to account links block link params if invitation
     * is allowed globally and for current website
     *
     * @return \Magento\Invitation\Block\Link
     */
    public function addAccountLink()
    {
        if (\Mage::getSingleton('Magento\Invitation\Model\Config')->isEnabledOnFront()
            && \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
        ) {
            /** @var $blockInstance \Magento\Page\Block\Template\Links */
            $blockInstance = $this->getLayout()->getBlock('account.links');
            if ($blockInstance) {
                $blockInstance->addLink(
                    __('Send Invitations'),
                    $this->_invitationData->getCustomerInvitationFormUrl(),
                    __('Send Invitations'),
                    true,
                    array(),
                    1,
                    'id="invitation-send-link"'
                );
            }
        }
        return $this;
    }

    /**
     * Adding link to account links block link params if invitation
     * is allowed globally and for current website
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return \Magento\Invitation\Block\Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (\Mage::getSingleton('Magento\Invitation\Model\Config')->isEnabledOnFront()) {
            /** @var $blockInstance \Magento\Customer\Block\Account\Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
