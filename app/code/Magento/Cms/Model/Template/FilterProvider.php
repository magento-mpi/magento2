<?php
/**
 * Cms Template Filter Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Model_Template_FilterProvider
{
    /**
     * @var Magento_ObjectManager
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
     * @param Magento_ObjectManager $objectManager
     * @param string $pageFilter
     * @param string $blockFilter
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        $pageFilter = 'Magento_Cms_Model_Template_Filter',
        $blockFilter = 'Magento_Cms_Model_Template_Filter'
    ) {
        $this->_objectManager = $objectManager;
        $this->_pageFilter = $pageFilter;
        $this->_blockFilter = $blockFilter;
    }

    /**
     * @param string $instanceName
     * @return Magento_Filter_Template
     * @throws Exception
     */
    protected function _getFilterInstance($instanceName)
    {
        if (!isset($this->_instanceList[$instanceName])) {
            $instance = $this->_objectManager->get($instanceName);

            if (!$instance instanceof Magento_Filter_Template) {
                throw new Exception('Template filter ' . $instanceName . ' does not implement required interface');
            }
            $this->_instanceList[$instanceName] = $instance;
        }

        return $this->_instanceList[$instanceName];
    }

    /**
     * @return Magento_Filter_Template
     */
    public function getBlockFilter()
    {
        return $this->_getFilterInstance($this->_blockFilter);
    }

    /**
     * @return Magento_Filter_Template
     */
    public function getPageFilter()
    {
        return $this->_getFilterInstance($this->_pageFilter);
    }
}
