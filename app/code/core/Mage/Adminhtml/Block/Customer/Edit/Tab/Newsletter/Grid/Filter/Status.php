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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
	protected static $_statuses = array(
		null										=>	null,
		Mage_Newsletter_Model_Queue::STATUS_SENT 	=> 'Sent',
		Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> 'Cancel',
		Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> 'Not Sent',
		Mage_Newsletter_Model_Queue::STATUS_SENDING => 'Sending',
		Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> 'Paused'
	);

	protected function _getOptions()
	{
		$result = array();
		foreach (self::$_statuses as $code=>$label) {
			$result[] = array('value'=>$code, 'label'=>__($label));
		}

		return $result;
	}


	public function getCondition()
	{
		if(is_null($this->getValue())) {
			return null;
		}

		return array('eq'=>$this->getValue());
	}
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Filter_Status END