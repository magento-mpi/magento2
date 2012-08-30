<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A custom "Import" adapter for Mage_ImportExport module that allows generating arbitrary data rows for a bunch
 * of customers
 */
class Magento_ImportExport_Fixture_Generator_CustomerComposite
    extends Magento_ImportExport_Fixture_Generator_Default
{
    /**
     * Columns, which have special generation rules applied
     * @var array
     */
    protected $_generateMethods = array(
        'created_at'            => '_generateCreatedAt',
        'firstname'             => '_generateName',
        'lastname'              => '_generateName',
        'gender'                => '_generateGender',
        '_address_firstname'    => '_generateName',
        '_address_lastname'     => '_generateName',
    );

    /**
     * Return whether column's value must be generated dynamically
     *
     * @param string $column
     * @param mixed $pattern
     * @return bool
     */
    protected function _isDynamicColumn($column, $pattern)
    {
        if (isset($this->_generateMethods[$column]) && $pattern = '%x') {
            return true;
        }
        return parent::_isDynamicColumn($column, $pattern);
    }

    /**
     * Generate value for a column
     *
     * @param string $column
     * @param string $pattern
     * @return mixed
     */
    protected function _generateValue($column, $pattern)
    {
        if (isset($this->_generateMethods[$column]) && $pattern = '%x') {
            $methodName = $this->_generateMethods[$column];
            return $this->$methodName();
        }
        return parent::_generateValue($column, $pattern);
    }

    /**
     * Generate value for 'created_at' column
     *
     * @return string
     */
    protected function _generateCreatedAt()
    {
        return date('d-m-Y H:i');
    }

    /**
     * Generate value for 'name', 'lastname' columns
     *
     * @return string
     */
    protected function _generateName()
    {
        $offsetA = ord('a');
        $result = '';
        $numLetters = rand(5, 10);
        for ($i = 0; $i < $numLetters; $i++) {
            $result .= chr ($offsetA + rand(0, 25));
        }
        return ucfirst($result);
    }

    /**
     * Generate value for 'gender' column
     *
     * @return string
     */
    protected function _generateGender()
    {
        return rand(0, 1) % 2 ? 'Male' : 'Female';
    }
}
