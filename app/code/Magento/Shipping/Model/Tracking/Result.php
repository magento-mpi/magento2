<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Shipping_Model_Tracking_Result
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
        if ($result instanceof Magento_Shipping_Model_Tracking_Result_Abstract) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof Magento_Shipping_Model_Rate_Result) {
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
