<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Data helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Cms_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_NODE_PAGE_TEMPLATE_FILTER     = 'global/cms/page/tempate_filter';
    const XML_NODE_BLOCK_TEMPLATE_FILTER    = 'global/cms/block/tempate_filter';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Template filter factory
     *
     * @var Magento_Cms_Model_Template_Filter_Factory
     */
    protected $_templateFilterFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Cms_Model_Template_Filter_Factory $templateFilterFactory
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Cms_Model_Template_Filter_Factory $templateFilterFactory
    ) {
        parent::__construct($context);
        $this->_coreConfig = $coreConfig;
        $this->_templateFilterFactory = $templateFilterFactory;
    }

    /**
     * Retrieve Template processor for Page Content
     *
     * @return Magento_Filter_Template
     */
    public function getPageTemplateProcessor()
    {
        $className = (string)$this->_coreConfig->getNode(self::XML_NODE_PAGE_TEMPLATE_FILTER);
        return $this->_templateFilterFactory->create($className);
    }

    /**
     * Retrieve Template processor for Block Content
     *
     * @return Magento_Filter_Template
     */
    public function getBlockTemplateProcessor()
    {
        $className = (string)$this->_coreConfig->getNode(self::XML_NODE_BLOCK_TEMPLATE_FILTER);
        return $this->_templateFilterFactory->create($className);
    }
}
