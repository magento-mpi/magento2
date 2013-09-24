<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page layout helper
 *
 * @category   Magento
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Helper_Layout extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Page_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Page_Model_Config $config
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Page_Model_Config $config,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Helper_Context $context
    ) {
        parent::__construct($context);
        $this->_layout = $layout;
        $this->_config = $config;
    }

    /**
     * Apply page layout handle
     *
     * @param string $pageLayout
     * @return Magento_Page_Helper_Layout
     */
    public function applyHandle($pageLayout)
    {
        $pageLayout = $this->_getConfig()->getPageLayout($pageLayout);

        if (!$pageLayout) {
            return $this;
        }

        $this->_layout->getUpdate()
            ->addHandle($pageLayout->getLayoutHandle());

        return $this;
    }

    /**
     * Apply page layout template
     * (for old design packages)
     *
     * @param string $pageLayout
     * @return Magento_Page_Helper_Layout
     */
    public function applyTemplate($pageLayout = null)
    {
        if ($pageLayout === null) {
            $pageLayout = $this->getCurrentPageLayout();
        } else {
            $pageLayout = $this->_getConfig()->getPageLayout($pageLayout);
        }

        if (!$pageLayout) {
            return $this;
        }

        if ($this->_layout->getBlock('root') &&
            !$this->_layout->getBlock('root')->getIsHandle()) {
                // If not applied handle
                $this->_layout->getBlock('root')
                    ->setTemplate($pageLayout->getTemplate());
        }

        return $this;
    }

    /**
     * Retrieve current applied page layout
     *
     * @return Magento_Object|boolean
     */
    public function getCurrentPageLayout()
    {
        if ($this->_layout->getBlock('root') &&
            $this->_layout->getBlock('root')->getLayoutCode()) {
            return $this->_getConfig()->getPageLayout($this->_layout->getBlock('root')->getLayoutCode());
        }

        // All loaded handles
        $handles = $this->_layout->getUpdate()->getHandles();
        // Handles used in page layouts
        $pageLayoutHandles = $this->_getConfig()->getPageLayoutHandles();
        // Applied page layout handles
        $appliedHandles = array_intersect($handles, $pageLayoutHandles);

        if (empty($appliedHandles)) {
            return false;
        }

        $currentHandle = array_pop($appliedHandles);

        $layoutCode = array_search($currentHandle, $pageLayoutHandles, true);

        return $this->_getConfig()->getPageLayout($layoutCode);
    }

    /**
     * Retrieve page config
     *
     * @return Magento_Page_Model_Config
     */
    protected function _getConfig()
    {
        return $this->_config;
    }
}
