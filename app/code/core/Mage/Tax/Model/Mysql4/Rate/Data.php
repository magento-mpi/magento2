<?php

class Mage_Tax_Model_Mysql4_Rate_Data extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('tax/rate_data', 'tax_rate_data_id');
	}
	
	public function fetchRate(Mage_Tax_Model_Rate_Data $request)
	{

		// initialize select from rate_data
		$select = $this->getConnection('read')->select();
		// get maximum rate from found
		$select->from(array('data'=>$this->getMainTable()), array('value'=>'max(rate_value)'));

		// join rule table with conditions
		$select->join(array('rule'=>$this->getTable('rule')), 'rule.tax_rate_type_id=data.rate_type_id');
		$select->where('rule.tax_customer_class_id=?', $request->getCustomerClassId());
		$select->where('rule.tax_product_class_id=?', $request->getProductClassId());
		
		// join rate table with conditions
		$select->join(array('rate'=>$this->getTable('rate')), 'rate.tax_rate_id=data.tax_rate_id');
		$select->where('rate.tax_region_id=?', $data->getCountyId());
		$select->where('rate.tax_postcode is null or rate.tax_postcode=?', $request->getCountyId());
		// for future county handling 
		if ($request->getCountyId()) {
			// TODO: make it play nice with zip
			$select->where('rate.tax_county_id is null or rate.tax_county_id=?', $request->getCountyId());
		}
		// retrieve all found rate data rows
		$rows = $this->getConnection('read')->fetchAll($select);

		return $rows ? $rows[0]['value'] : 0;
	}
}