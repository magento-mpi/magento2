<?php
/**
 * Mage newsletter problem resource model
 *
 * @package    Mage
 * @subpackage Newsletter
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Newsletter_Model_Mysql4_Problem extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('newsletter/problem', 'problem_id');
	}
	
}// Class Mage_Newsletter_Model_Mysql4_Problem END