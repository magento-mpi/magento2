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
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_NODE_PAGE_TEMPLATE_FILTER     = 'global/cms/page/tempate_filter';
    const XML_NODE_BLOCK_TEMPLATE_FILTER    = 'global/cms/block/tempate_filter';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $coreConfig
    ) {
        parent::__construct(
            $context
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Retrieve Template processor for Page Content
     *
     * @return \Magento\Filter\Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string)$this->_coreConfig->getNode(self::XML_NODE_PAGE_TEMPLATE_FILTER);
        return \Mage::getModel($model);
    }

    /**
     * Retrieve Template processor for Block Content
     *
     * @return \Magento\Filter\Template
     */
    public function getBlockTemplateProcessor()
    {
        $model = (string)$this->_coreConfig->getNode(self::XML_NODE_BLOCK_TEMPLATE_FILTER);
        return \Mage::getModel($model);
    }
}
