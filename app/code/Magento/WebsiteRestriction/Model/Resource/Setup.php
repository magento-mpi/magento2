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
namespace Magento\WebsiteRestriction\Model\Resource;

class Setup extends \Magento\Module\Setup
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Module\Setup\Context $context,
        $resourceName,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $moduleName = 'Magento_WebsiteRestriction',
        $connectionName = ''
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
