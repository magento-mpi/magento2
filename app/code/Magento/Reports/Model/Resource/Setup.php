<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource Setup Model
 */
class Magento_Reports_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Cms_Model_PageFactory $pageFactory,
        $resourceName,
        $moduleName = 'Magento_Reports',
        $connectionName = ''
    ) {
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
