<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Tax_Model_System_Config_Source_Tax_Country extends Magento_Directory_Model_Config_Source_Country
{
    protected $_options;

    public function toOptionArray($noEmpty=false)
    {
        $options = parent::toOptionArray($noEmpty);

        if(!$noEmpty) {
            if ($options) {
                $options[0]['label'] = __('None');
            } else {
                $options = array(array('value'=>'', 'label'=>__('None')));
            }
        }

        return $options;
    }
}
