<?php
/**
 * Subscription grid container
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mage_Webhook';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_subscription';

    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_headerText      = $this->__('Subscriptions');
        $this->_addButtonLabel  = $this->__('Add Subscription');
    }
}
