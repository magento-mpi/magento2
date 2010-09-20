<?php
/**
 * Frontend framework model
 *
 * @author Magento Inc.
 */
class Model_Frontend extends TestModelAbstract
{

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->baseUrl = Core::getEnvConfig('frontend/baseUrl');
        $this->setBrowserUrl($this->baseUrl);
        $this->userName = Core::getEnvConfig('frontend/auth/email');
        $this->password = Core::getEnvConfig('frontend/auth/password');
    }
}

