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
namespace Magento\Reports\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $resourceName,
        $moduleName = 'Magento_Reports',
        $connectionName = ''
    ) {
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
