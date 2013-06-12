<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Pbridge_Model_System_Config_Source_Cctype extends Varien_Object
{
    /**
     * Return allowed cc types for current method
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        $configPathCcTypesAll = $this->getPath() . '_all';
        $ccTypes = Mage::getStoreConfig($configPathCcTypesAll);
        $ccTypes = explode(',', $ccTypes);
        $ccTypes = array_map('trim', $ccTypes);

        return $ccTypes;
    }

    /**
     * Return list of supported CC type codes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $model = Mage::getModel('Mage_Payment_Model_Source_Cctype')->setAllowedTypes($this->getAllowedTypes());
        return $model->toOptionArray();
    }
}
