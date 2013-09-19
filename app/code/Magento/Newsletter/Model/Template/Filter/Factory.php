<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template filter factory
 */
class Magento_Newsletter_Model_Template_Filter_Factory
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
     * Create template filter
     *
     * @param string $className
     * @param array $data
     * @return Magento_Filter_Template
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof Magento_Filter_Template) {
            throw new Magento_Core_Exception($className . ' doesn\'t extends Magento_Filter_Template');
        }
        return $filter;
    }
}
