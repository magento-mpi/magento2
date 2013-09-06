<?php
/**
 * Configuration value backend model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Data_BackendModelPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config_Data_BackendModelInterface[]
     */
    protected $_pool;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get backend model instance
     *
     * @param string $model
     * @return Magento_Core_Model_Config_Data_BackendModelInterface
     * @throws InvalidArgumentException
     */
    public function get($model)
    {
        if (!isset($this->_pool[$model])) {
            $instance = $this->_objectManager->create($model);
            if (!($instance instanceof Magento_Core_Model_Config_Data_BackendModelInterface)) {
                throw new InvalidArgumentException(
                    $model . ' does not instance of Magento_Core_Model_Config_Data_BackendModelInterface'
                );
            }
            $this->_pool[$model] = $instance;
        }
        return $this->_pool[$model];
    }
}
