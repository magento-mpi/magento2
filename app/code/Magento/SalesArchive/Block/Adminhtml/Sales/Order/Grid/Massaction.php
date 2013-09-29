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
 *  Add sales archiving to order's grid view massaction
 *  @deprecated
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid;

class Massaction
    extends \Magento\Adminhtml\Block\Widget\Grid\Massaction\AbstractMassaction
{
    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_configModel;

    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\SalesArchive\Model\Config $configModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\SalesArchive\Model\Config $configModel,
        array $data = array()
    ) {
        $this->_configModel = $configModel;
        parent::__construct($backendData, $coreData, $context, $data);
    }


    /**
     * Before rendering html operations
     *
     * @return \Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid\Massaction
     */
    protected function _beforeToHtml()
    {
        $isActive = $this->_configModel->isArchiveActive();
        if ($isActive && $this->_authorization->isAllowed('Magento_SalesArchive::add')) {
            $this->addItem('add_order_to_archive', array(
                 'label'=> __('Move to Archive'),
                 'url'  => $this->getUrl('*/sales_archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
