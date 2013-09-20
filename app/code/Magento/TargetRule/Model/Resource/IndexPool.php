<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * TargetRule Product Index Pool
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Resource_IndexPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get resource index singleton
     *
     * @param string $type
     * @param array $arguments
     * @throws LogicException
     * @return Magento_TargetRule_Model_Resource_Index_Abstract
     */
    public function get($type, array $arguments = array())
    {
        switch ($type) {
            case Magento_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $model = 'Related';
                break;
            case Magento_TargetRule_Model_Rule::UP_SELLS:
                $model = 'Upsell';
                break;
            case Magento_TargetRule_Model_Rule::CROSS_SELLS:
                $model = 'Crosssell';
                break;
            default:
                throw new LogicException($type . ' is undefined catalog product list type');
        }

        $className = 'Magento_TargetRule_Model_Resource_Index_' . $model;
        $indexResource = $this->_objectManager->get($className, $arguments);

        if (false === ($indexResource instanceof Magento_TargetRule_Model_Resource_Index_Abstract)) {
            throw new LogicException(
                $className . ' doesn\'t extend Magento_TargetRule_Model_Resource_Index_Abstract'
            );
        }

        return $indexResource;
    }
}
