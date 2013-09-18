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
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab;

class Hierarchy
    extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Array of nodes for tree
     * @var array|null
     */
    protected $_nodes = null;
    
    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsHierarchy = $cmsHierarchy;
        parent::__construct($coreData, $context, $data);
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
        return $this->_coreData->jsonEncode($this->getNodes());
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
                $data = $this->_coreData->jsonDecode($this->getPage()->getNodesData());
            }catch (\Zend_Json_Exception $e){
                $data = null;
            }

            $collection = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node')->getCollection()
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
                    if ($item = $collection->getItemById($v['node_id'])) {
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

        return $this->_coreData->jsonEncode($data);
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
            || !$this->_authorization->isAllowed('Magento_VersionsCms::hierarchy'))
        {
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
