<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\View\Tab;

/**
 * Order transactions tab
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Transactions extends \Magento\Framework\View\Element\Text\ListText implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\AuthorizationInterface $authorization,
        array $data = array()
    ) {
         $this->_authorization = $authorization;
         parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Transactions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Transactions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->_authorization->isAllowed('Magento_Sales::transactions_fetch');
    }
}
