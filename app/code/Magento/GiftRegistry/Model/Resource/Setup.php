<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry resource setup
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory
     * @param Magento_Core_Helper_Data $coreHelper
     * @param Magento_Core_Model_Config $config
     * @param Magento_GiftRegistry_Model_TypeFactory $typeFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Eav_Model_Resource_Entity_Attribute_Group_CollectionFactory $attrGrCollFactory,
        Magento_Core_Helper_Data $coreHelper,
        Magento_Core_Model_Config $config,
        Magento_GiftRegistry_Model_TypeFactory $typeFactory,
        $resourceName,
        $moduleName = 'Magento_GiftRegistry',
        $connectionName = ''
    ) {
        $this->_typeFactory = $typeFactory;
        parent::__construct(
            $context, $cache, $attrGrCollFactory, $coreHelper, $config, $resourceName, $moduleName, $connectionName
        );
    }
}
