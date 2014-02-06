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
    /**
     * @var array
     */
    protected $_trackings = array();

    /**
     * @var null|array
     */
    protected $_error = null;

    /**
     * Reset tracking
     *
     * @return $this
     */
    public function reset()
    {
        $this->_trackings = array();
        return $this;
    }

    /**
     * @param array $error
     * @return void
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * @return array|null
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Add a tracking to the result
     *
     * @param \Magento\Shipping\Model\Tracking\Result\AbstractResult|\Magento\Shipping\Model\Rate\Result $result
     * @return $this
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
     *
     * @return array
     */
    public function getAllTrackings()
    {
        return $this->_trackings;
    }

}
