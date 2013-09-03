<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Model_Url_Factory
{
    /**
     * Default url model class name
     */
    const CLASS_NAME = 'Magento_Core_Model_Url';

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
     * Replace name of url model
     *
     * @param string $className
     * @return Magento_DesignEditor_Model_Url_Factory
     */
    public function replaceClassName($className)
    {
        $this->_objectManager->configure(array('preferences' => array(self::CLASS_NAME => $className)));

        return $this;
    }

    /**
     * Create url model new instance
     *
     * @param array $arguments
     * @return Magento_Core_Model_Url
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
