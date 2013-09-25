<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer filter factory
 */
class Magento_Catalog_Model_Layer_Filter_Factory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create layer filter
     *
     * @param string $className
     * @param array $data
     * @return Magento_Catalog_Model_Layer_Filter_Attribute
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof Magento_Catalog_Model_Layer_Filter_Abstract) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Catalog_Model_Layer_Filter_Abstract');
        }
        return $filter;
    }
}
