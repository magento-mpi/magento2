<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Proxy for extended template block for Visual Design Editor
 * @category   Mage
 * @package    Mage_DesignEditor
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_DesignEditor_Block_Template_Proxy extends Mage_DesignEditor_Block_Template
{
    /**
     * Name of entity class
     */
    const ENTITY_CLASS = 'Mage_DesignEditor_Block_Template';

    /**
     * Object manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve template object
     *
     * @return Mage_DesignEditor_Block_Template
     */
    protected function _getTemplate()
    {
        return $this->_objectManager->get(self::ENTITY_CLASS);
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return Mage_DesignEditor_Block_Template
     */
    public function setData($key, $value = null)
    {
        return $this->_getTemplate()->setData($key, $value);
    }
}
