<?php
/**
 * Adminhtml newsletter subscriber grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber extends Mage_Core_Block_Template 
{
	public function __construct() 
	{
		$this->setTemplate('adminhtml/newsletter/subscriber/list.phtml');
	}
	
	protected function _beforeToHtml() 
	{
		$this->setChild('grid', $this->getLayout()->createBlock('adminhtml/newsletter_subscriber_grid','grid'));
		return parent::_beforeToHtml();
	}
}