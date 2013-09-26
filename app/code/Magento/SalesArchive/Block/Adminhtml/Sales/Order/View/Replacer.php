<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive order view replacer for archive
 *
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View;

class Replacer
    extends \Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_configModel;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\SalesArchive\Model\Config $configModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\SalesArchive\Model\Config $configModel,
        array $data = array()
    ) {
        $this->_configModel = $configModel;
        parent::__construct($coreData, $context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_shipments',
                'Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_invoices',
                'Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_creditmemos',
                'Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab\Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if ($this->_authorization->isAllowed('Magento_SalesArchive::remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                    'label' => __('Move to Order Managment'),
                    'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                    'class' => 'cancel'
                ));
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = $this->_configModel->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    '*/sales_archive/add',
                    array('order_id' => $this->getOrder()->getId())
                );
                if ($this->_authorization->isAllowed('Magento_SalesArchive::add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                        'label' => __('Move to Archive'),
                        'onclick' => 'setLocation(\'' . $archiveUrl . '\')',
                    ));
                }
            }
        }

        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
