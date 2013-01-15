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

class Saas_PrintedTemplate_Model_Variable_FakeTax extends Saas_PrintedTemplate_Model_Variable_Tax
{
    protected function _setListsFromConfig($variableName)
    {
        $fields = array(
            'total_amount' => array('type' => 'currency'),
            'base_total_amount' => array('type' => 'currency'),
            'tax_amount' => array('type' => 'currency'),
            'base_tax_amount' => array('type' => 'currency'),
            'tax_amount_without_discount' => array('type' => 'currency'),
            'total_amount_without_discount' => array('type' => 'currency'),
            'is_tax_after_discount' => array()
        );

        foreach ($fields as $name => $config) {
            $type = isset($config['type']) ? $config['type'] : 'text';
            $this->_addPropertyToList($name, $type);
            $this->_addMethodToList('get' . $this->_camelize($name), $type);
        }

        return $this;
    }

    public function formatCurrency($value)
    {
        return $value;
    }
}
