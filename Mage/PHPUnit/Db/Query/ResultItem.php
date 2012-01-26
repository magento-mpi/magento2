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
 * Class which contains one query data.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Query_ResultItem
{
    /**
     * Error message, which was taken from '<error>' tag
     *
     * @var string
     */
    protected $_errorMessage;

    /**
     * Query result data
     *
     * @var mixed
     */
    protected $_result;

    /**
     * Sets error message for query (if we want to emulate wrong query)
     *
     * @param string $message
     * @return Mage_PHPUnit_Db_Query_ResultItem
     */
    public function setErrorMessage($message)
    {
        $this->_errorMessage = $message;
        return $this;
    }

    /**
     * Returns error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Sets query result
     *
     * @param mixed $result
     * @return Mage_PHPUnit_Db_Query_ResultItem
     */
    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    /**
     * Returns query result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }
}
