<?php
/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Customer extends Model_Admin
{

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->customerId = Core::getEnvConfig('backend/customer/id');
    }
}

