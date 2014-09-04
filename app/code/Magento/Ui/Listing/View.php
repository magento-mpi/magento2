<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing;

use Magento\Ui\AbstractView;
use \Magento\Backend\Block\Template\Context;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

/**
 * Class View
 */
class View extends AbstractView
{
    /**
     * @var \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @param Context $context
     * @param UrlBuilder $actionUrlBuilder
     * @param array $data
     */
    public function __construct(Context $context, UrlBuilder $actionUrlBuilder, array $data = [])
    {
        $this->actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $data);

        $this->initialConfiguration();
    }

    /**
     * Get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->getData('dataSource');
    }

    /**
     * return array
     */
    protected function getCollectionItems()
    {
        $collection = $this->getCollection();
        $rows = $collection->getItems();
        $items = [];
        /** @var \Magento\Cms\Model\Page $row */
        foreach ($rows as $row) {
            $item = [];
            $item['id'] = $row->getId();
            $item['store_id'] = 'All Store Views';
            $item['title'] = $row->getTitle();
            $item['url'] = $row->getIdentifier();
            $item['layout'] = $row->getPageLayout();
            $item['created'] = $row->getCreationTime();
            $item['modified'] = $row->getUpdateTime();
            $item['status'] = boolval($row->getIsActive());
            $item['action'] = [
                'href' => $this->actionUrlBuilder->getUrl(
                    $row->getIdentifier(),
                    $row->getData('_first_store_id'),
                    $row->getStoreCode()
                ),
                'title' => 'Preview'
            ];
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getMetaFields()
    {
        return [
            [
                'title' => 'Title',
                'id' => 'title'
            ],
            [
                'title' => 'URL Key',
                'id' => 'url',
                'sorted' => 'desc'
            ],
            [
                'title' => 'Layout',
                'id' => 'page_layout'
            ],
            [
                'title' => 'Store View',
                'id' => 'store_id'
            ],
            [
                'title' => 'Status',
                'id' => 'status'
            ],
            [
                'title' => 'Created',
                'id' => 'created'
            ],
            [
                'title' => 'Modified',
                'id' => 'modified'
            ],
            [
                'title' => 'Action',
                'id' => 'action'
            ],
        ];
    }

    /**
     * @return void
     */
    protected function initialConfiguration()
    {
        $result = [
            'config' => [
                'namespace' => 'cms.pages'
            ]
        ];

        $result['meta']['fields'] = $this->getMetaFields();
        $result['data']['items'] = $this->getCollectionItems();

        $countItems = $this->getCollection()->count();
        $result['data']['pages'] = ceil($countItems / 5);
        $result['data']['totalCount'] = $countItems;

        $this->configuration = array_merge($this->configuration, $result);
    }
}
