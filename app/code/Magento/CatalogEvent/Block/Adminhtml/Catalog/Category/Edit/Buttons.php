<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Catalog Events edit form select categories
 */
namespace Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Resource\Category\Tree;
use Magento\CatalogEvent\Helper\Data;
use Magento\CatalogEvent\Model\Event;
use Magento\CatalogEvent\Model\Resource\Event\Collection;
use Magento\CatalogEvent\Model\Resource\Event\CollectionFactory;
use Magento\Framework\Registry;

class Buttons extends AbstractCategory
{
    /**
     * Factory for event collections
     *
     * @var CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * @var Data
     */
    protected $_catalogeventHelper;

    /**
     * @var BackendHelperData
     */
    protected $_backendHelper;

    /**
     * @param Context $context
     * @param Tree $categoryTree
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $eventCollectionFactory
     * @param Data $catalogeventHelper
     * @param BackendHelperData $backendHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Tree $categoryTree,
        Registry $registry,
        CategoryFactory $categoryFactory,
        CollectionFactory $eventCollectionFactory,
        Data $catalogeventHelper,
        BackendHelperData $backendHelper,
        array $data = []
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_catalogeventHelper = $catalogeventHelper;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);

        $this->_eventCollectionFactory = $eventCollectionFactory;
    }

    /**
     * Retrieve category event
     *
     * @return Event
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            /** @var Collection $collection */
            $collection = $this->_eventCollectionFactory->create()->addFieldToFilter(
                'category_id',
                $this->getCategoryId()
            );

            $event = $collection->getFirstItem();
            $this->setData('event', $event);
        }

        return $this->getData('event');
    }

    /**
     * Add buttons on category edit page
     *
     * @return $this
     */
    public function addButtons()
    {
        if ($this->_catalogeventHelper->isEnabled() && $this->_authorization->isAllowed(
            'Magento_CatalogEvent::events'
        ) && $this->getCategoryId() && $this->getCategory()->getLevel() > 1
        ) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->_backendHelper->getUrl(
                    'adminhtml/catalog_event/edit',
                    ['id' => $this->getEvent()->getId(), 'category' => 1]
                );
                $this->getParentBlock()->getChildBlock(
                    'form'
                )->addAdditionalButton(
                    'edit_event',
                    [
                        'label' => __('Edit Event...'),
                        'class' => 'save',
                        'onclick' => 'setLocation(\'' . $url . '\')'
                    ]
                );
            } else {
                $url = $this->_backendHelper->getUrl(
                    'adminhtml/catalog_event/new',
                    ['category_id' => $this->getCategoryId(), 'category' => 1]
                );
                $this->getParentBlock()->getChildBlock(
                    'form'
                )->addAdditionalButton(
                    'add_event',
                    [
                        'label' => __('Add Event...'),
                        'class' => 'add',
                        'onclick' => 'setLocation(\'' . $url . '\')'
                    ]
                );
            }
        }
        return $this;
    }
}
