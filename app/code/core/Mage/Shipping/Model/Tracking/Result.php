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
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Shipping_Model_Tracking_Result
{

	protected $_trackings = array();
	protected $_error = null;

	/**
	 * Reset tracking
	 */
	public function reset()
	{
	    $this->_trackings = array();
	    return $this;
	}

	public function setError($error)
	{
	    $this->_error = $error;
	}

	public function getError()
	{
	    return $this->_error;
	}
	/**
	 * Add a tracking to the result
	 */
	public function append($result)
	{
	    if ($result instanceof Mage_Shipping_Model_Tracking_Result_Abstract) {
	        $this->_trackings[] = $result;
	    } elseif ($result instanceof Mage_Shipping_Model_Rate_Result) {
	        $trackings = $result->getAllTrackings();
	        foreach ($trackings as $track) {
	            $this->append($track);
	        }
	    }
	    return $this;
	}

	/**
	 * Return all trackings in the result
	 */
	public function getAllTrackings()
	{
		return $this->_trackings;
	}

}