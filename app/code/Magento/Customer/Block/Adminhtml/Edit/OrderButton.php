<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Ui\Component\Control\ButtonProviderInterface;

/**
 * Class OrderButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class OrderButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->authorization = $context->getAuthorization();
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId && $this->authorization->isAllowed('Magento_Sales::create')) {
            $data = [
                'label' => __('Create Order'),
                'on_click' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\')',
                'class' => 'add',
                'sort_order' => 40
            ];
        }
        return $data;
    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('sales/order_create/start', array('customer_id' => $this->getCustomerId()));
    }
}
