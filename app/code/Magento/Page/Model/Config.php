<?php
/**
 * Page layout config model
 * 
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Page_Model_Config
{
    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts = null;

    /** @var  Magento_Page_Model_Config_Data */
    protected $_dataStorage;

    /**
     * Constructor
     *
     * @param Magento_Config_Data $dataStorage
     */
    public function __construct(
        Magento_Config_Data $dataStorage
    ) {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Initialize page layouts list
     *
     * @return Magento_Page_Model_Config
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = array();
            foreach ($this->_dataStorage->get() as $layoutCode => $layoutConfig) {
                $this->_pageLayouts[$layoutCode] = new Magento_Object($layoutConfig);
            }
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return array
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
     * @return Magento_Object|boolean
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
