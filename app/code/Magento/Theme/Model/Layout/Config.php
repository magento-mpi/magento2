<?php
/**
 * Page layout config model
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Model\Layout;

class Config
{
    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts = null;

    /** @var  \Magento\Framework\Config\DataInterface */
    protected $_dataStorage;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Config\DataInterface $dataStorage
     */
    public function __construct(\Magento\Framework\Config\DataInterface $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Initialize page layouts list
     *
     * @return \Magento\Theme\Model\Layout\Config
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = array();
            foreach ($this->_dataStorage->get(null) as $layoutCode => $layoutConfig) {
                $layoutConfig['label'] = __($layoutConfig['label']);
                $this->_pageLayouts[$layoutCode] = new \Magento\Framework\Object($layoutConfig);
            }
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return \Magento\Framework\Object[]
     */
    public function getPageLayouts()
    {
        $this->_initPageLayouts();
        return $this->_pageLayouts;
    }

    /**
     * Retrieve page layout by code
     *
     * @param string $layoutCode
     * @return \Magento\Framework\Object|boolean
     */
    public function getPageLayout($layoutCode)
    {
        $this->_initPageLayouts();

        if (isset($this->_pageLayouts[$layoutCode])) {
            return $this->_pageLayouts[$layoutCode];
        }

        return false;
    }

    /**
     * Retrieve page layout handles
     *
     * @return array
     */
    public function getPageLayoutHandles()
    {
        $handles = array();

        foreach ($this->getPageLayouts() as $layout) {
            $handles[$layout->getCode()] = $layout->getLayoutHandle();
        }

        return $handles;
    }
}
