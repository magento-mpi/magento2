<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscribers grid block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml\Subscriber;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * @var \Magento\Newsletter\Model\QueueFactory
     */
    protected $_queueFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Newsletter\Model\QueueFactory $queueFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Newsletter\Model\QueueFactory $queueFactory,
        array $data = array()
    ) {
        $this->_queueFactory = $queueFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        if ($this->getRequest()->getParam('queue', false)) {
            $this->getCollection()->useQueue($this->_queueFactory->create()
                ->load($this->getRequest()->getParam('queue'))
            );
        }

        return parent::_prepareCollection();
    }
}
