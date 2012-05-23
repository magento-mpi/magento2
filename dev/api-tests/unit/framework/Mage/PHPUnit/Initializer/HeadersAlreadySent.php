<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Initializer of Mage::$headersSentThrowsException flag
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Initializer_HeadersAlreadySent extends Mage_PHPUnit_Initializer_Abstract
{
    /**
     * Previous value of Mage::$headersSentThrowsException flag
     *
     * @var bool
     */
    protected $_previousHeadersAlreadySent;

    /**
     * New value of Mage::$headersSentThrowsException flag
     *
     * @var bool
     */
    protected $_headersAlreadySentThrowsException = false;

    /**
     * Runs initialization process.
     */
    public function run()
    {
        $this->_previousHeadersAlreadySent = Mage::$headersSentThrowsException;
        Mage::$headersSentThrowsException = $this->getThrowException();
    }

    /**
     * Rollback all changes after the test is ended (on tearDown)
     */
    public function reset()
    {
        Mage::$headersSentThrowsException = (bool)$this->_previousHeadersAlreadySent;
    }

    /**
     * Sets new value of Mage::$headersSentThrowsException flag.
     * Really will be set after calling run() method of the initializer
     *
     * @param bool $headersAlreadySentThrowsException
     * @return Mage_PHPUnit_Initializer_HeadersAlreadySent
     */
    public function setThrowException($headersAlreadySentThrowsException)
    {
        $this->_headersAlreadySentThrowsException = (bool)$headersAlreadySentThrowsException;
        return $this;
    }

    /**
     * Returns value of Mage::$headersSentThrowsException flag which will be set.
     *
     * @return bool
     */
    public function getThrowException()
    {
        return $this->_headersAlreadySentThrowsException;
    }
}
