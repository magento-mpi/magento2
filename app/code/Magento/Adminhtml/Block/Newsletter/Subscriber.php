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

class Magento_Adminhtml_Block_Newsletter_Subscriber extends Magento_Adminhtml_Block_Template
{
    /**
     * Queue collection
     *
     * @var Magento_Newsletter_Model_Resource_Queue_Collection
     */
    protected $_queueCollection = null;

    protected $_template = 'newsletter/subscriber/list.phtml';

    /**
     * @var Magento_Newsletter_Model_Resource_Queue_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Newsletter_Model_Resource_Queue_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Newsletter_Model_Resource_Queue_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepares block to render
     *
     * @return Magento_Adminhtml_Block_Newsletter_Subscriber
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * Return queue collection with loaded neversent queues
     *
     * @return Magento_Newsletter_Model_Resource_Queue_Collection
     */
    public function getQueueCollection()
    {
        if (is_null($this->_queueCollection)) {
            /** @var $this->_queueCollection Magento_Newsletter_Model_Resource_Queue_Collection */
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
