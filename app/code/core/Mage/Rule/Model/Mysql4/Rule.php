<?php

/**
 * Quote rule mysql4 resource model
 *
 * @package    Mage
 * @subpackage Rule
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Rule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('rule/rule', 'rule_id');
	}
}