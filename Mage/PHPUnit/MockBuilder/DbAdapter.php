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
 * DbAdapter mock object creator for constructions such as
 * $this->_getReadAdapter(), $this->_getWriteAdapter() or $this->_getConnection('core_read'),
 * which are called in Resource models.
 * It can be useful for creating mocks for adapter methods such as 'query', 'delete', 'insert', 'fetchAll', etc.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_DbAdapter extends Mage_PHPUnit_MockBuilder_Abstract
{
    /**
     * Connection name
     *
     * @var string
     */
    protected $_connectionName;

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_TestCase $testCase
     * @param string $connectionName Example: 'core_read'
     */
    public function __construct(PHPUnit_Framework_TestCase $testCase, $connectionName)
    {
        $this->testCase  = $testCase;
        $this->_connectionName = $connectionName;
        $this->className = $this->_getConnectionHelper()->getConnectionClassName($connectionName);
        $this->setConstructorArgs(array($this->_getConnectionHelper()->getConnectionConfig($connectionName)));
    }

    /**
     * Returns connection's name. Only getter.
     * For another connection please create another builder.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->_connectionName;
    }

    /**
     * Returns PHPUnit connection's helper.
     *
     * @return Mage_PHPUnit_Helper_Connection
     */
    protected function _getConnectionHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('connection');
    }

    /**
     * Method which is called after getMock() method.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _afterGetMock($mock)
    {
        $this->_setMockToConfig($mock);
    }

    /**
     * Sets mock object to Config connection array.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _setMockToConfig($mock)
    {
        $this->_getConnectionHelper()->setConnection($this->getConnectionName(), $mock);
    }
}
