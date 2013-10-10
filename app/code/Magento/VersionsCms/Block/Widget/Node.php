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
 * Cms Hierarchy Node Widget Block
 */
namespace Magento\VersionsCms\Block\Widget;

class Node
    extends \Magento\Core\Block\Html\Link
    implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Current Hierarchy Node Page Instance
     *
     * @var \Magento\VersionsCms\Model\Hierarchy\Node
     */
    protected $_node;

    /**
     * Current Store Id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve specified anchor text
     *
     * @return string
     */
    public function getAnchorText()
    {
        $value = $this->_getInstanceData('anchor_text');

        return ($value !== false ? $value : $this->_node->getLabel());
    }

    /**
     * Retrieve link specified title
     *
     * @return string
     */
    public function getTitle()
    {
        $value = $this->_getInstanceData('title');

        return ($value !== false ? $value : $this->_node->getLabel());
    }

    /**
     * Retrieve Node ID
     *
     * @return mixed|null
     */
    public function getNodeId()
    {
        return $this->_getInstanceData('node_id');
    }

    /**
     * Retrieve Node URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->_node->getUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getNodeId()) {
            $this->_node = $this->_hierarchyNodeFactory->create()->load($this->getNodeId());
        } else {
            $this->_node = $this->_coreRegistry->registry('current_cms_hierarchy_node');
        }

        if (!$this->_node) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    protected function _getStoreId()
    {
        if (null === $this->_storeId) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve data from instance
     *
     * @param string $key
     * @return bool|mixed
     */
    protected function _getInstanceData($key)
    {
        $dataKeys = array(
            $key . '_' . $this->_getStoreId(),
            $key . '_' . \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID,
            $key,
        );
        foreach($dataKeys as $value) {
            if ($this->getData($value) !== null) {
               return $this->getData($value);
            }
        }
        return false;
    }
}
