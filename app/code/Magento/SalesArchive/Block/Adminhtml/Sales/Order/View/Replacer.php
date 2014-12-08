<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View;

/**
 * Sales archive order view replacer for archive
 */
class Replacer extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_configModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\SalesArchive\Model\Config $configModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\SalesArchive\Model\Config $configModel,
        array $data = []
    ) {
        $this->_configModel = $configModel;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $restoreUrl = $this->getUrl(
                'sales/archive/remove',
                ['order_id' => $this->getOrder()->getId()]
            );
            if ($this->_authorization->isAllowed('Magento_SalesArchive::remove')) {
                $this->getLayout()->getBlock(
                    'sales_order_edit'
                )->addButton(
                    'restore',
                    [
                        'label' => __('Move to Order Managment'),
                        'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                        'class' => 'cancel'
                    ]
                );
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = $this->_configModel->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    'sales/archive/add',
                    ['order_id' => $this->getOrder()->getId()]
                );
                if ($this->_authorization->isAllowed('Magento_SalesArchive::add')) {
                    $this->getLayout()->getBlock(
                        'sales_order_edit'
                    )->addButton(
                        'restore',
                        ['label' => __('Move to Archive'), 'onclick' => 'setLocation(\'' . $archiveUrl . '\')']
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }
}
