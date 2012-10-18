<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Currency_Service
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $services = Mage::getConfig()->getNode('global/currency/import/services')->asArray();
            $this->_options = array();
            foreach( $services as $_code => $_options ) {
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
