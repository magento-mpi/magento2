<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms Page Edit Hierarchy Tab Block
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab;

class Hierarchy
    extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Array of nodes for tree
     *
     * @var array|null
     */
    protected $_nodes = null;

    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Hierarchy\Node\CollectionFactory
     */
    protected $_nodeCollectionFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Json\DecoderInterface $jsonDecoder
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\VersionsCms\Model\Resource\Hierarchy\Node\CollectionFactory $nodeCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Json\DecoderInterface $jsonDecoder,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Core\Model\Registry $registry,
        \Magento\VersionsCms\Model\Resource\Hierarchy\Node\CollectionFactory $nodeCollectionFactory,
        array $data = array()
    ) {
        $this->_jsonDecoder = $jsonDecoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_nodeCollectionFactory = $nodeCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current page instance
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_coreRegistry->registry('cms_page');
    }

    /**
     * Retrieve Hierarchy JSON string
     *
     * @return string
     */
    public function getNodesJson()
    {
        return $this->_jsonEncoder->encode($this->getNodes());
    }

    /**
     * Prepare nodes data from DB  all from session if error occurred.
     *
     * @return array
     */
    public function getNodes() {
        if (is_null($this->_nodes)) {
            $this->_nodes = array();
            try{
                $data = $this->_jsonDecoder->decode($this->getPage()->getNodesData());
            }catch (\Zend_Json_Exception $e){
                $data = null;
            }

            /** @var \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection $collection */
            $collection = $this->_nodeCollectionFactory->create()
                ->joinCmsPage()
                ->setOrderByLevel()
                ->joinPageExistsNodeInfo($this->getPage());

            if (is_array($data)) {
                foreach ($data as $v) {
                    if (isset($v['page_exists'])) {
                        $pageExists = (bool)$v['page_exists'];
                    } else {
                        $pageExists = false;
                    }
                    $node = array(
                        'node_id'               => $v['node_id'],
                        'parent_node_id'        => $v['parent_node_id'],
                        'label'                 => $v['label'],
                        'page_exists'           => $pageExists,
                        'page_id'               => $v['page_id'],
                        'current_page'          => (bool)$v['current_page']
                    );
                    $item = $collection->getItemById($v['node_id']);
                    if ($item) {
                        $node['assigned_to_stores'] = $this->getPageStoreIds($item);
                    } else {
                        $node['assigned_to_stores'] = array();
                    }

                    $this->_nodes[] = $node;
                }
            } else {
                foreach ($collection as $item) {
                    if ($item->getLevel() == \Magento\VersionsCms\Model\Hierarchy\Node::NODE_LEVEL_FAKE) {
                        continue;
                    }
                    /* @var $item \Magento\VersionsCms\Model\Hierarchy\Node */
                    $node = array(
                        'node_id'               => $item->getId(),
                        'parent_node_id'        => $item->getParentNodeId(),
                        'label'                 => $item->getLabel(),
                        'page_exists'           => (bool)$item->getPageExists(),
                        'page_id'               => $item->getPageId(),
                        'current_page'          => (bool)$item->getCurrentPage(),
                        'assigned_to_stores'    => $this->getPageStoreIds($item)

                    );
                    $this->_nodes[] = $node;
                }
            }
        }
        return $this->_nodes;
    }

    /**
     * @param object $node
     * @return array
     */
    public function getPageStoreIds($node)
    {
        if (!$node->getPageId() || !$node->getPageInStores()) {
            return array();
        }
        return explode(',', $node->getPageInStores());
    }

    /**
     * Forced nodes setter
     *
     * @param array $nodes New nodes array
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab\Hierarchy
     */
    public function setNodes($nodes)
    {
        if (is_array($nodes)) {
            $this->_nodes = $nodes;
        }

        return $this;
    }

    /**
     * Retrieve ids of selected nodes from two sources.
     * First is from prepared data from DB.
     * Second source is data from page model in case we had error.
     *
     * @return string
     */
    public function getSelectedNodeIds()
    {
        if (!$this->getPage()->hasData('node_ids')) {
            $ids = array();

            foreach ($this->getNodes() as $node) {
                if (isset($node['page_exists']) && $node['page_exists']) {
                    $ids[] = $node['node_id'];
                }
            }
            return implode(',', $ids);
        }

        return $this->getPage()->getData('node_ids');
    }

    /**
     * Prepare json string with current page data
     *
     * @return string
     */
    public function getCurrentPageJson()
    {
        $data = array(
            'label' => $this->getPage()->getTitle(),
            'id' => $this->getPage()->getId()
        );

        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Hierarchy');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Hierarchy');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (!$this->getPage()->getId()
            || !$this->_cmsHierarchy->isEnabled()
            || !$this->_authorization->isAllowed('Magento_VersionsCms::hierarchy')
        ) {
            return false;
        }
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
