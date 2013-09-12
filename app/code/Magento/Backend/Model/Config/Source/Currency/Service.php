<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Currency_Service implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $services = Mage::getConfig()->getNode('global/currency/import/services')->asArray();
            $this->_options = array();
            foreach ($services as $_code => $_options ) {
                $this->_options[] = array(
                    'label' => $_options['name'],
                    'value' => $_code,
                );
            }
        }

        $options = $this->_options;
        return $options;
    }

}
