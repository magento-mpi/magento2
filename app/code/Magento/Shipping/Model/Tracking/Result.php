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

use Magento\Shipping\Model\Tracking\Result\AbstractResult;
use Magento\Shipping\Model\Rate\Result as RateResult;

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
     * @param AbstractResult|RateResult $result
     * @return $this
     */
    public function append($result)
    {
        if ($result instanceof AbstractResult) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof RateResult) {
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
