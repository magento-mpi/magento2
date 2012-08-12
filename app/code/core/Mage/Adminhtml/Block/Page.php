<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml page
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page extends Mage_Adminhtml_Block_Template
{

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('admin/page.phtml');
        $action = Mage::app()->getFrontController()->getAction();
        if ($action) {
            $this->addBodyClass($action->getFullActionName('-'));
        }
    }

    /**
     * Get current language
     *
     * @return unknown
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return Mage_Adminhtml_Block_Page
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }

}
