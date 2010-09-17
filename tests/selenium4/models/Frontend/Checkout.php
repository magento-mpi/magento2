<?php
/**
 * Frontend_checkout model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Checkout extends Model_Frontend {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->userData = Core::getEnvConfig('backend/user');
    }
}
