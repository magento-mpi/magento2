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
	/**
	 * Queue collection
	 *
	 * @var Mage_Newsletter_Model_Mysql4_Queue_Collection
	 */
	protected $_queueCollection = null;
	
	/**
	 * Constructor
	 *
	 * Initializes block
	 */
	public function __construct() 
	{
		$this->setTemplate('newsletter/subscriber/list.phtml');
	}
	
	/**
	 * Prepares block to render
	 *
	 * @return Mage_Adminhtml_Block_Newsletter_Subscriber
	 */
	protected function _beforeToHtml() 
	{
		$this->setChild('grid', $this->getLayout()->createBlock('adminhtml/newsletter_subscriber_grid','grid'));
		return parent::_beforeToHtml();
	}
	
	/**
	 * Return queue collection with loaded neversent queues
	 *
	 * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
	 */
	public function getQueueCollection() 
	{
		if(is_null($this->_queueCollection)) {
			$this->_queueCollection = Mage::getResourceSingleton('newsletter/queue_collection')
				->addTemplateInfo()
				->addOnlyUnsentFilter()
				->load();
		}
		
		return $this->_queueCollection;
	}
	
	public function getShowQueueAdd() 
    {
    	return $this->getChild('grid')->getShowQueueAdd();
    }
	
	/**
	 * Return list of neversent queues for select
	 *
	 * @return array
	 */
	public function getQueueAsOptions( ) 
	{
		return $this->getQueueCollection()->toOptionArray();
	}
}