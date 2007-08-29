<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rate collection
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rate_Collection extends Varien_Data_Collection_Db
{
    protected $_rateTable;

    protected $_rateTypeTable;

    protected $_rateDataTable;

    protected $_regionTable;

    protected $_postcodeTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_rateTable     = $resource->getTableName('tax/tax_rate');
        $this->_rateTypeTable = $resource->getTableName('tax/tax_rate_type');
        $this->_rateDataTable = $resource->getTableName('tax/tax_rate_data');
        $this->_regionTable   = $resource->getTableName('directory/country_region');
        $this->_postcodeTable = $resource->getTableName('usa/postcode');
        $this->_sqlSelect->from($this->_rateTable);
    }

    public function addAttributes()
    {
        $rateTypes = Mage::getResourceModel('tax/rate_type_collection')->load()->getItems();
        foreach( $rateTypes as $type ) {
            $tableAlias = "trd_{$type->getTypeId()}";
            $this->_sqlSelect->joinLeft(array(
            	$tableAlias => $this->_rateDataTable), 
            	"{$this->_rateTable}.tax_rate_id = {$tableAlias}.tax_rate_id AND {$tableAlias}.rate_type_id = '{$type->getTypeId()}'", array(
            		"rate_value_{$type->getTypeId()}" => 'rate_value',
            	)
            );
        }

        $this->_sqlSelect->joinLeft($this->_regionTable, "{$this->_rateTable}.tax_region_id = {$this->_regionTable}.region_id", array('region_name' => 'code'));
        #$this->_sqlSelect->joinLeft($this->_postcodeTable, "{$this->_postcodeTable}.county = {$this->_rateTable}.tax_county_id", array('county_name' => 'county')); /* FIXME!!! */
        return $this;
    }

    public function loadRatesData()
    {
        $this->_sqlSelect->from($this->_rateDataTable);
        return parent::load();
    }
}