<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model\Tracking;

class Result
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
        if ($result instanceof \Magento\Shipping\Model\Tracking\Result\AbstractResult) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof \Magento\Shipping\Model\Rate\Result) {
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
