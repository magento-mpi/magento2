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
        $this->_baseUrl = Core::getEnvConfig('backend/baseUrl');
        $this->_userName = Core::getEnvConfig('backend/auth/username');
        $this->_password = Core::getEnvConfig('backend/auth/password');
    }

}

