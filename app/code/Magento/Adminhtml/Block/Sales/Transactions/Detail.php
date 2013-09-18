<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml transaction detail
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Transactions;

class Detail extends \Magento\Adminhtml\Block\Widget\Container
{
    /**
     * Transaction model
     *
     * @var \Magento\Sales\Model\Order\Payment\Transaction
     */
    protected $_txn;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add control buttons
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_txn = $this->_coreRegistry->registry('current_transaction');
        if (!$this->_txn) {
            return;
        }

        $backUrl = ($this->_txn->getOrderUrl()) ? $this->_txn->getOrderUrl() : $this->getUrl('*/*/');
        $this->_addButton('back', array(
            'label'   => __('Back'),
            'onclick' => "setLocation('{$backUrl}')",
            'class'   => 'back'
        ));

        if ($this->_authorization->isAllowed('Magento_Sales::transactions_fetch')
            && $this->_txn->getOrderPaymentObject()->getMethodInstance()->canFetchTransactionInfo()) {
            $fetchUrl = $this->getUrl('*/*/fetch' , array('_current' => true));
            $this->_addButton('fetch', array(
                'label'   => __('Fetch'),
                'onclick' => "setLocation('{$fetchUrl}')",
                'class'   => 'button'
            ));
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __("Transaction # %1 | %2", $this->_txn->getTxnId(), $this->formatDate($this->_txn->getCreatedAt(), \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true));
    }

    protected function _toHtml()
    {
        $this->setTxnIdHtml($this->escapeHtml($this->_txn->getTxnId()));

        $this->setParentTxnIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_transactions/view', array('txn_id' => $this->_txn->getParentId())))
        );

        $this->setParentTxnIdHtml(
            $this->escapeHtml($this->_txn->getParentTxnId())
        );

        $this->setOrderIncrementIdHtml($this->escapeHtml($this->_txn->getOrder()->getIncrementId()));

        $this->setTxnTypeHtml($this->escapeHtml($this->_txn->getTxnType()));

        $this->setOrderIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_order/view', array('order_id' => $this->_txn->getOrderId())))
        );

        $this->setIsClosedHtml(
            ($this->_txn->getIsClosed()) ? __('Yes') : __('No')
        );

        $createdAt = (strtotime($this->_txn->getCreatedAt()))
            ? $this->formatDate($this->_txn->getCreatedAt(), \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true)
            : __('N/A');
        $this->setCreatedAtHtml($this->escapeHtml($createdAt));

        return parent::_toHtml();
    }
}
