<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml page
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block;

class Page extends \Magento\Backend\Block\Template
{

    protected $_template = 'admin/page.phtml';

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $action = \Mage::app()->getFrontController()->getAction();
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
            $this->setData('lang', substr(\Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return \Magento\Adminhtml\Block\Page
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
