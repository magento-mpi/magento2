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

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * Block model factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
     * @param $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $resourceName,
        $moduleName = 'Magento_Enterprise',
        $connectionName = ''
    ) {
        $this->_modelBlockFactory = $modelBlockFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \\Magento\Cms\Model\Block
     */
    public function getModelBlock()
    {
        return $this->_modelBlockFactory->create();
    }
}
