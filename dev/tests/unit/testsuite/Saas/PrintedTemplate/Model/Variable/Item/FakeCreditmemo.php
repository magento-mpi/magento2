<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_Item_FakeCreditmemo
    extends Saas_PrintedTemplate_Model_Variable_Item_Creditmemo
{

    protected function _setListsFromConfig($variableName)
    {
        $fields = array(
            'discount_amount' => array('type' => 'currency'),
            'row_total_inc' => array('type' => 'currency'),
            'discount_rate' => array('type' => 'percent'),
            'price_incl_tax' => array('type' => 'currency'),
            'row_total_incl_discount_and_tax' => array('type' => 'currency'),
        );

        foreach ($fields as $name => $config) {
            $type = isset($config['type']) ? $config['type'] : 'text';
            $this->_addPropertyToList($name, $type);
            $this->_addMethodToList('get' . $this->_camelize($name), $type);
        }

        return $this;
    }

    protected function _getLocale()
    {
        return 'en_US';
    }
}
