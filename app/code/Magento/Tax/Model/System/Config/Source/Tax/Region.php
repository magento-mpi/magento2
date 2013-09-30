<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_System_Config_Source_Tax_Region implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    /**
     * @var Magento_Directory_Model_Resource_Region_Collection_Factory
     */
    protected $_regionsFactory;

    /**
     * @param Magento_Directory_Model_Resource_Region_Collection_Factory $regionsFactory
     */
    public function __construct(Magento_Directory_Model_Resource_Region_Collection_Factory $regionsFactory)
    {
        $this->_regionsFactory = $regionsFactory;
    }

    public function toOptionArray($noEmpty = false, $country = null)
    {
        /** @var $region Magento_Directory_Model_Resource_Region_Collection */
        $regionCollection = $this->_regionsFactory->create();
        $options = $regionCollection->addCountryFilter($country)->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0]['label'] = '*';
            } else {
                $options = array(array('value' => '', 'label' => '*'));
            }
        }

        return $options;
    }
}
