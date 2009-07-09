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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Mage Global TestSuite
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Retrieve DB Adapter instance for Test
     *
     * @return Mage_DbAdapter
     */
    protected function _getDbAdapter()
    {
        return Mage::registry('_dbadapter');
    }

    /**
     * Setup Test Suite and begin transaction
     *
     */
    public function setUp()
    {
        $this->_getDbAdapter()->begin();
        parent::setUp();

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            session_id(md5(time()));
            $_SESSION = array();
        }
    }

    /**
     * Tear down Test Suite and rollback transaction
     *
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->_getDbAdapter()->rollback();
    }
}
