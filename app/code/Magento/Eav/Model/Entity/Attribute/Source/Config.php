<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * this class should be abstract, but kept usual for legacy purposes
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Source_Config extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var array
     */
    protected $_optionsData;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_optionsData = $options;
    }

    /**
     * Retrieve all options for the source from configuration
     *
     * @throws Magento_Eav_Exception
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();

            if (empty($this->_optionsData)) {
                throw Mage::exception('Magento_Eav', __('No options found'));
            }
            foreach ($this->_optionsData as $option) {
                $this->_options[] = array(
                    'value' => $option['value'],
                    'label' => __($option['label'])
                );
            }
        }

        return $this->_options;
    }
}
