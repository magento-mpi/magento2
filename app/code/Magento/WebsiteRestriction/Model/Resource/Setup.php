<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Private sales setup model resource
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_WebsiteRestriction_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Cms_Model_PageFactory $pageFactory,
        $resourceName,
        $moduleName = 'Magento_WebsiteRestriction',
        $connectionName = ''
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
