<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation;

/**
 * Invitation view block
 *
 */
class Add extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_objectId = 'invitation_id';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Invitation';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_invitation';

    /**
     * @var string
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
