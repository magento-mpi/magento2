<?php
/**
 * Abstract test class for Admin module
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Abstract extends Test_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    public function  setUp() {
        parent::setUp();

        $this->_helper = Core::getHelper('admin');

        // Get test parameters
        // Should be loaded from some config
        $this->_baseurl = "http://kq.varien.com/enterprise/1.8.0.0/index.php/control/index/";
        $this->_username = "admin";
        $this->_password = "123123q";
    }

}

