<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_Item_FakeInvoice
    extends Saas_PrintedTemplate_Model_Variable_Item_Invoice
{

    protected function _setListsFromConfig($variableName)
    {
        $fields = array(
            'discount' => array('type' => 'currency'),
            'discount_amount' => array('type' => 'currency'),
            'discount_amount_excl_tax' => array('type' => 'currency'),
            'discount_excl_tax' => array('type' => 'currency'),
            'price_inc' => array('type' => 'currency'),
            'price_incl_discount' => array('type' => 'currency'),
            'price_incl_discount_excl_tax' => array('type' => 'currency'),
            'row_total_incl_discount' => array('type' => 'currency'),
            'row_total_incl_discount_excl_tax' => array('type' => 'currency'),
            'row_total_incl_discount_and_tax' => array('type' => 'currency'),
            'discount_rate' => array('type' => 'percent'),
            'price_incl_tax' => array('type' => 'currency')
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
