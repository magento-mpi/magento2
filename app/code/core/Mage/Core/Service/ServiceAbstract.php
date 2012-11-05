<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Service Layer
 */
abstract class Mage_Core_Service_ServiceAbstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_translateHelper;

    /**
     * Constructor
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        if (isset($args['config'])) {
            $this->_config = $args['config'];
        } else {
            $this->_config = Mage::getConfig();
        }

        if (isset($args['helper'])) {
            $this->_translateHelper =  $args['helper'];
        } else {
            $this->_translateHelper =  Mage::helper('Mage_Core_Helper_Data');
        }
    }

    /**
     * Sets each value from data to entity Varien_Object using setter method.
     *
     * @param Varien_Object $entity
     * @param array $data
     */
    protected function _setDataUsingMethods($entity, array $data)
    {
        foreach ($data as $property => $value) {
            $entity->setDataUsingMethod($property, $value);
        }
    }
}
