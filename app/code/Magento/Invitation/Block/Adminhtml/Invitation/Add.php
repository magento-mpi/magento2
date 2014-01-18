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
 * Invitation view block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation;

class Add extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @inheritdoc
     */
    protected $_objectId = 'invitation_id';

    /**
     * @inheritdoc
     */
    protected $_blockGroup = 'Magento_Invitation';

    /**
     * @inheritdoc
     */
    protected $_controller = 'adminhtml_invitation';

    /**
     * @inheritdoc
     */
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
