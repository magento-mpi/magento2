<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reports_Model_Product_Index_Factory
{
    const TYPE_COMPARED = 'compared';
    const TYPE_VIEWED = 'viewed';

    /**
     * @var array
     */
    protected $_typeClasses = array(
        self::TYPE_COMPARED => 'Magento_Reports_Model_Product_Index_Compared',
        self::TYPE_VIEWED => 'Magento_Reports_Model_Product_Index_Viewed'
    );

    /**
     * @var Magento_Reports_Model_Product_Index_Abstract[]
     */
    protected $_instances;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return Magento_Reports_Model_Product_Index_Abstract
     * @throws InvalidArgumentException
     */
    public function get($type)
    {
        if (!isset($this->_instances[$type])) {
            if (!isset($this->_typeClasses[$type])) {
                throw new InvalidArgumentException("{$type} is not index model");
            }
            $this->_instances[$type] = $this->_objectManager->create($this->_typeClasses[$type]);
        }
        return $this->_instances[$type];
    }
}
