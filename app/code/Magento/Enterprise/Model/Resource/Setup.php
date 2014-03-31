<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise resource setup
 */
namespace Magento\Enterprise\Model\Resource;

class Setup extends \Magento\Module\Setup
{
    /**
     * Block model factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * @param \Magento\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Module\Setup\Context $context,
        $resourceName,
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $moduleName = 'Magento_Enterprise',
        $connectionName = ''
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
