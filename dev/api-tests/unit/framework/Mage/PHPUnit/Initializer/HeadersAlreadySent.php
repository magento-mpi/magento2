<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
