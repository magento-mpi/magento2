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
class Magento_Enterprise_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Block model factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_modelBlockFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Cms_Model_BlockFactory $modelBlockFactory,
        $resourceName,
        $moduleName = 'Magento_Enterprise',
        $connectionName = ''
    ) {
        $this->_modelBlockFactory = $modelBlockFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return \Magento_Cms_Model_Block
     */
    public function getModelBlock()
    {
        return $this->_modelBlockFactory->create();
    }
}
