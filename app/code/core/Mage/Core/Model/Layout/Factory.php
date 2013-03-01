<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_Factory
{
    /**
     * Default layout class name
     */
    const CLASS_NAME = 'Mage_Core_Model_Layout';

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
     * @param array $arguments
     * @param string $className
     * @return Mage_Core_Model_Layout
     */
    public function createLayout(array $arguments = array(), $className = self::CLASS_NAME)
    {
        $configuration = array(
            $className => array(
                'parameters' => $arguments
            )
        );
        if ($className != self::CLASS_NAME) {
            $configuration['preferences'] = array(
                self::CLASS_NAME => $className,
            );
        }
        $this->_objectManager->configure($configuration);
        return $this->_objectManager->get(self::CLASS_NAME);
    }
}
