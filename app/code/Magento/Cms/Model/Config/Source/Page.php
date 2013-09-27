<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Cms_Model_Config_Source_Page implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    /**
     * @var Magento_Cms_Model_Resource_Page_CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * @param Magento_Cms_Model_Resource_Page_CollectionFactory $pageCollectionFactory
     */
    public function __construct(Magento_Cms_Model_Resource_Page_CollectionFactory $pageCollectionFactory)
    {
        $this->_pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_pageCollectionFactory->create()->load()->toOptionIdArray();
        }
        return $this->_options;
    }

}
