<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Data_Form_Factory
{
    /**
     * @varMagento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManger
     */
    public function __construct(Magento_ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Create Magento data form with provided params
     *
     * @param array $attributes
     * @return Magento_Data_Form
     */
    public function create(array $attributes = array())
    {
        return $this->_objectManager->create('Magento_Data_Form', array('attributes' => $attributes));
    }
}
