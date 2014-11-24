<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Widget;

/**
 * Cms Hierarchy Node Widget Block
 */
class Node extends \Magento\Framework\View\Element\Html\Link implements \Magento\Widget\Block\BlockInterface
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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve specified anchor text
     *
     * @return string
     */
    public function getLabel()
    {
        $value = $this->_getInstanceData('label');

        return $value !== false ? $value : $this->_node->getLabel();
    }

    /**
     * Retrieve link specified title
     *
     * @return string
     */
    public function getTitle()
    {
        $value = $this->_getInstanceData('title');

        return $value !== false ? $value : $this->_node->getLabel();
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
     * Retrieve anchor text
     *
     * @return false|mixed
     */
    public function getAnchorText()
    {
        $value = $this->_getInstanceData('anchor_text');

        return $value !== false ? $value : $this->getLabel();
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
     * @return mixed|false
     */
    protected function _getInstanceData($key)
    {
        $dataKeys = array(
            $key . '_' . $this->_getStoreId(),
            $key . '_' . \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            $key
        );
        foreach ($dataKeys as $value) {
            if ($this->getData($value) !== null) {
                return $this->getData($value);
            }
        }
        return false;
    }
}
