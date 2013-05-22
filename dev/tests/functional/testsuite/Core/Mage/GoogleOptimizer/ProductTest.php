<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_ProductTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        parent::setUpBeforeTests();

        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');
    }

    /**
     * @_test
     * @group goinc
     */
    public function checkBehaviorOnCreate()
    {
    }

    /**
     * @_test
     * @group goinc
     */
    public function checkBehaviorOnUpdate()
    {
    }

    /**
     * @_test
     * @group goinc
     */
    public function checkBehaviorIfDisabled()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_disable');
    }
}
