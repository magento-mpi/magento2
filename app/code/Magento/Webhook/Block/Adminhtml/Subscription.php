<?php
/**
 * Subscription grid container
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml;

class Subscription extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Webhook';

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

        $this->_headerText      = __('Subscriptions');
        $this->_addButtonLabel  = __('Add Subscription');
    }
}
