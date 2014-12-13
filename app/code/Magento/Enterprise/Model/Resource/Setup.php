<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Enterprise resource setup
 */
namespace Magento\Enterprise\Model\Resource;

class Setup extends \Magento\Framework\Module\DataSetup
{
    /**
     * Block model factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $moduleName = 'Magento_Enterprise',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_modelBlockFactory = $modelBlockFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento\Cms\Model\Block
     */
    public function getModelBlock()
    {
        return $this->_modelBlockFactory->create();
    }
}
