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
 * Adminhtml newsletter subscriber grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Newsletter;

class Subscriber extends \Magento\Adminhtml\Block\Template
{
    /**
     * Queue collection
     *
     * @var \Magento\Newsletter\Model\Resource\Queue\Collection
     */
    protected $_queueCollection = null;

    protected $_template = 'newsletter/subscriber/list.phtml';

    /**
     * @var \Magento\Newsletter\Model\Resource\Queue\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Prepares block to render
     *
     * @return \Magento\Adminhtml\Block\Newsletter\Subscriber
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * Return queue collection with loaded neversent queues
     *
     * @return \Magento\Newsletter\Model\Resource\Queue\Collection
     */
    public function getQueueCollection()
    {
        if (is_null($this->_queueCollection)) {
            /** @var $this->_queueCollection \Magento\Newsletter\Model\Resource\Queue\Collection */
            $this->_queueCollection = $this->_collectionFactory->create()
                ->addTemplateInfo()
                ->addOnlyUnsentFilter()
                ->load();
        }

        return $this->_queueCollection;
    }

    public function getShowQueueAdd()
    {
        return $this->getChildBlock('grid')->getShowQueueAdd();
    }

    /**
     * Return list of neversent queues for select
     *
     * @return array
     */
    public function getQueueAsOptions()
    {
        return $this->getQueueCollection()->toOptionArray();
    }
}
