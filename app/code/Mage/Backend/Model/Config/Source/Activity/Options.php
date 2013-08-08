<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Activity_Options
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * @param Mage_Core_Helper_Abstract $helper
     */
    public function __construct(Mage_Core_Helper_Abstract $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Active'),
            '0' => __('Inactive'),
        );
    }
}


