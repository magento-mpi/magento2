<?php
/**
 * Review helper
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Block_Helper extends Mage_Core_Block_Template
{
	public function getSummaryHtml($product, $type=null)
	{
	    if( !$product->getRatingSummary() ) {
	        Mage::getModel('review/review')
	           ->getEntitySummary($product);
	    }

	    switch ($type) {
	    	case 'short':
	    		$this->setTemplate('review/helper/summary_short.phtml');
	    		break;

	    	default:
	    		$this->setTemplate('review/helper/summary.phtml');
	    		break;
	    }

		$this->setProduct($product);
		return $this->toHtml();
	}
}