<?php
/**
 * Page layout config model
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout\PageType;

class Config
{
    /**
     * Available page types
     *
     * @var array
     */
    protected $_pageTypes = null;

    /** @var  \Magento\Config\DataInterface */
    protected $_dataStorage;

    /**
     * Constructor
     *
     * @param \Magento\Config\DataInterface $dataStorage
     */
    public function __construct(
        \Magento\Config\DataInterface $dataStorage
    ) {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Initialize page types list
     *
     * @return \Magento\View\Layout\PageType\Config
     */
    protected function _initPageTypes()
    {
        if ($this->_pageTypes === null) {
            $this->_pageTypes = array();
            foreach ($this->_dataStorage->get(null) as $pageTypeId => $pageTypeConfig) {
                $pageTypeConfig['label'] = __($pageTypeConfig['label']);
                $this->_pageTypes[$pageTypeId] = new \Magento\Object($pageTypeConfig);
            }
        }
        return $this;
    }

    /**
     * Retrieve available page types
     *
     * @return array \Magento\Object[]
     */
    public function getPageTypes()
    {
        $this->_initPageTypes();
        return $this->_pageTypes;
    }
}
