<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation view block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Add extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'invitation_id';
    protected $_blockGroup = 'Enterprise_Invitation';
    protected $_controller = 'adminhtml_invitation';
    protected $_mode = 'add';

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('New Invitations');
    }

}
