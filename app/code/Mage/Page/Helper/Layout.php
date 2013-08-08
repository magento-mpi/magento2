<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page layout helper
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Helper_Layout extends Magento_Core_Helper_Abstract
{
    /**
     * Apply page layout handle
     *
     * @param string $pageLayout
     * @return Mage_Page_Helper_Layout
     */
    public function applyHandle($pageLayout)
    {
        $pageLayout = $this->_getConfig()->getPageLayout($pageLayout);

        if (!$pageLayout) {
            return $this;
        }

        $this->getLayout()
            ->getUpdate()
            ->addHandle($pageLayout->getLayoutHandle());

        return $this;
    }

    /**
     * Apply page layout template
     * (for old design packages)
     *
     * @param string $pageLayout
     * @return Mage_Page_Helper_Layout
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

        if ($this->getLayout()->getBlock('root') &&
            !$this->getLayout()->getBlock('root')->getIsHandle()) {
                // If not applied handle
                $this->getLayout()
                    ->getBlock('root')
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
        if ($this->getLayout()->getBlock('root') &&
            $this->getLayout()->getBlock('root')->getLayoutCode()) {
            return $this->_getConfig()->getPageLayout($this->getLayout()->getBlock('root')->getLayoutCode());
        }

        // All loaded handles
        $handles = $this->getLayout()->getUpdate()->getHandles();
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
     * @return Mage_Page_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Mage_Page_Model_Config');
    }
}
