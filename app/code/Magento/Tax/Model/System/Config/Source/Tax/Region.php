<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Tax\Model\System\Config\Source\Tax;

class Region implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray($noEmpty=false, $country = null)
    {
        $options = \Mage::getModel('Magento\Directory\Model\Region')
            ->getCollection()
            ->addCountryFilter($country)
            ->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0]['label'] = '*';
            } else {
                $options = array(array('value'=>'', 'label'=>'*'));
            }
        }

        return $options;
    }
}
