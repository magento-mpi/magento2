<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_Factory
{
    /**
     * Default layout class name
     */
    const CLASS_NAME = 'Magento_Core_Model_Layout';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $arguments
     * @param string $className
     * @return Magento_Core_Model_Layout
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
