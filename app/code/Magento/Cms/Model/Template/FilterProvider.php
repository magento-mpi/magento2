<?php
/**
 * Cms Template Filter Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Template;

/**
 * Filter provider model
 */
class FilterProvider
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_pageFilter;

    /**
     * @var string
     */
    protected $_blockFilter;

    /**
     * @var array
     */
    protected $_instanceList;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $pageFilter
     * @param string $blockFilter
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $pageFilter = 'Magento\Cms\Model\Template\Filter',
        $blockFilter = 'Magento\Cms\Model\Template\Filter'
    ) {
        $this->_objectManager = $objectManager;
        $this->_pageFilter = $pageFilter;
        $this->_blockFilter = $blockFilter;
    }

    /**
     * @param string $instanceName
     * @return \Magento\Filter\Template
     * @throws \Exception
     */
    protected function _getFilterInstance($instanceName)
    {
        if (!isset($this->_instanceList[$instanceName])) {
            $instance = $this->_objectManager->get($instanceName);

            if (!$instance instanceof \Magento\Filter\Template) {
                throw new \Exception('Template filter ' . $instanceName . ' does not implement required interface');
            }
            $this->_instanceList[$instanceName] = $instance;
        }

        return $this->_instanceList[$instanceName];
    }

    /**
     * @return \Magento\Filter\Template
     */
    public function getBlockFilter()
    {
        return $this->_getFilterInstance($this->_blockFilter);
    }

    /**
     * @return \Magento\Filter\Template
     */
    public function getPageFilter()
    {
        return $this->_getFilterInstance($this->_pageFilter);
    }
}
