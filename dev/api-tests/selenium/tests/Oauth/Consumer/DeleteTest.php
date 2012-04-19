<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test deleting consumer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     {license_link}
 */
class Oauth_Consumer_DeleteTest extends Mage_Selenium_TestCase
{
    /*
      * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> oAuth -> Consumers</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('oauth_consumers');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Delete consumer.</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1. Search and open consumer.</p>
     * <p>2. Click 'Delete' button.</p>
     * <p>Expected result:</p>
     * <p>Consumer is deleted.</p>
     * <p>Success Message is displayed.</p>
     *
     * @test
     */

    //Failed because https://jira.magento.com/browse/APIA-199

    public function deleteConsumer()
    {
        //Data
        $consumerData = $this->loadData('generic_consumer');
        //Preconditions
        $this->oauthHelper()->createConsumer($consumerData);
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Steps
        $this->addParameter('consumer_search_name', $consumerData['consumer_name']);
        $this->oauthHelper()->openConsumer(array('name' => $consumerData['consumer_name']));
        $this->clickButtonAndConfirm('delete_consumer', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_consumer');
    }
}
