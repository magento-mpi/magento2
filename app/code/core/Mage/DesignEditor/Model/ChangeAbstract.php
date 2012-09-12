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
 * Changes factory model. Creates right Change instance by given type.
 */
abstract class Mage_DesignEditor_Model_ChangeAbstract extends Varien_Object
{
    /**
     * Generic constructor of change instance
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct();
        $this->_validate();
    }

    /**
     * Signature of validation method to implement in subclasses
     *
     * @abstract
     * @throws Exception
     * @return Mage_DesignEditor_Model_ChangeAbstract
     */
    abstract protected function _validate();
}
