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
 * Adminhtml newsletter subscriber grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber extends Mage_Adminhtml_Block_Template
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