<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Comments History Block at RMA page
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class History
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{
    /**
     * @var \Magento\Rma\Model\Config
     */
    protected $_rmaConfig;

    /**
     * @var \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Model\Config $rmaConfig
     * @param \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Model\Config $rmaConfig,
        \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_rmaConfig = $rmaConfig;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $registry, $data);
    }

    /**
     * Prepare child blocks
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\History
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('rma-history-block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * Get config value - is Enabled RMA Comments Email
     *
     * @return bool
     */
    public function canSendCommentEmail()
    {
        $this->_rmaConfig->init($this->_rmaConfig->getRootCommentEmail(), $this->getOrder()->getStore());
        return $this->_rmaConfig->isEnabled();
    }

    /**
     * Get config value - is Enabled RMA Email
     *
     * @return bool
     */
    public function canSendConfirmationEmail()
    {
        $this->_rmaConfig->init($this->_rmaConfig->getRootRmaEmail(), $this->getOrder()->getStore());
        return $this->_rmaConfig->isEnabled();
    }

    /**
     * Get URL to add comment action
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('id'=>$this->getRmaData('entity_id')));
    }

    /**
     * @return \Magento\Rma\Model\Resource\Rma\Status\History\Collection
     */
    public function getComments()
    {
        /** @var $collection \Magento\Rma\Model\Resource\Rma\Status\History\Collection */
        $collection = $this->_collectionFactory->create();
        return $collection->addFieldToFilter('rma_entity_id', $this->_coreRegistry->registry('current_rma')->getId());
    }
}
