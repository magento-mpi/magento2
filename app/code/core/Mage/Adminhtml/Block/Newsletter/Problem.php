<?php
/**
 * Adminhtml newsletter problem block template.
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Problem extends Mage_Core_Block_Template 
{
	public function __construct() 
	{
		parent::__construct();
		$this->setTemplate('adminhtml/newsletter/problem/list.phtml');
	}
	
	public function _initChildren()
	{
		$this->setChild('grid', 
			$this->getLayout()->createBlock('adminhtml/newsletter_problem_grid','newsletter.problem.grid')
		);
	}
}// Class Mage_Adminhtml_Block_Newsletter_Problem END