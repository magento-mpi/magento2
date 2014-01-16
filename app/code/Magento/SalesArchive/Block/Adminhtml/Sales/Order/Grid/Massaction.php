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
    extends \Magento\Backend\Block\Widget\Grid\Massaction\Extended
{
    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_configModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\SalesArchive\Model\Config $configModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\SalesArchive\Model\Config $configModel,
        array $data = array()
    ) {
        $this->_configModel = $configModel;
        parent::__construct($context, $jsonEncoder, $backendData, $data);
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
                 'url'  => $this->getUrl('sales/archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
