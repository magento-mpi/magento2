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
 * Test editing consumer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     {license_link}
 */
class Oauth_Consumer_EditTest extends Mage_Selenium_TestCase
{
    /**
     * Consumer name
     *
     * @var string
     */
    protected $_consumerToBeDeleted;

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

     protected function tearDown()
    {
        if ($this->_consumerToBeDeleted) {
            $this->navigate('oauth_consumers');
            $this->oauthHelper()->deleteConsumerByName($this->_consumerToBeDeleted);
            $this->_consumerToBeDeleted = null;
        }
    }
    /**
     * <p>Edit consumer. All new data is valid</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1. Search and open consumer.</p>
     * <p>2. Fill all fiels with new value</p>
     * <p>3. Click Save button</p>
     * <p>Expected result:</p>
     * <p>Consumer has been saved.</p>
     * <p>Success Message is displayed.</p>
     * <p>4. Select Consumer from preconditions.</p>
     * <p>Expected result:</p>
     * <p>Edit consumer form is opened.
     * Verify value of all fields.
     * Attention! Need to add dependsis
     * @test
     */
    public function withAllValidData()
    {
       //Data
        $consumerData = $this->loadData('generic_consumer');
        $searchData = array('name' => $consumerData['consumer_name']);
        //Preconditions
        $this->oauthHelper()->createConsumer($consumerData);
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Steps
        //Open consumer
        $this->addParameter('consumer_search_name', $consumerData['consumer_name']);
        $this->oauthHelper()->openConsumer($searchData);
        //Remember key and secret
        $key = $this->oauthHelper()->getFieldValue('key');
        $secret = $this->oauthHelper()->getFieldValue('secret');
        //Fill all new data
        $newConsumerData = $this->loadData('new_consumer_data');
        $this->oauthHelper()->editConsumer($newConsumerData, $searchData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Open edited Consumer
        $this->addParameter('new_consumer_search_name', $newConsumerData['consumer_name']);
        $this->oauthHelper()->openConsumer(array('name' => $newConsumerData['consumer_name']));
        // Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $newConsumerData['consumer_name'];
        //Verify value of name, callback_url and rejected_callback_url fields
        foreach ($newConsumerData as $field => $value) {
            $this->assertEquals($value, $this->oauthHelper()->getFieldValue($field), $field . ' does not match.');
        }
        //Verify value of key and secret
        $this->assertEquals($key, $this->oauthHelper()->getFieldValue('key'), 'Key does not match.');
        $this->assertEquals($secret, $this->oauthHelper()->getFieldValue('secret'), 'Secret does not match.');
    }

    /**
     * <p>Edit consumer. Name field is invalid</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1. Search and open consumer.</p>
     * <p>2. Clear Name field</p>
     * <p>3. Click Save button</p>
     * <p>Expected result:</p>
     * <p>Consumer is not saved.</p>
     * <p>Error Message is displayed.</p>
     * Verify value of all fields.
     * @test
     */
    public function editWithRequiredFieldEmpty()
    {
        //Preconditions
        $consumerData = $this->loadData('generic_consumer');
        $this->oauthHelper()->createConsumer($consumerData);
        // Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Steps
        //Open created Consumer
        $this->oauthHelper()->openConsumer(array('name' => $consumerData['consumer_name']));
        //Set Name field to '' and try to save Consumer
        $consumerData['consumer_name'] = '';
        $this->oauthHelper()->editConsumer($consumerData);
        //Verifying message and page
        $xpath = $this->oauthHelper()->getUIMapFieldXpath('edit_consumer', 'consumer_name');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->checkCurrentPage('edit_consumer'), $this->getParsedMessages());
    }

    /**
     * <p>Edit consumer with invalid value for 'Rejected Url' field</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1.Search and open consumer.</p>
     * <p>2. Fill Rejected URL with "invalid url" text.</p>
     * <p>3. Click 'Save' button.</p>
     * <p> Expected result:</p>
     * <p> Message "Please enter a valid URL. Protocol is required (http://, https:// or ftp://)"
     * appears under 'Rejected Callback Url' field. Edit Consumer page is opened.</p>
     * <p>3. Click 'Reset' button.</p>
     * <p> Expected result:</p>
     * <p> Verify that Rejected URL value does not change</p>
     *
     * @param string $wrongUrl
     * @depends withAllValidData
     * @dataProvider withInvalidUrlDataProvider
     * @test
     */
    public function withInvalidRejectedURL($wrongUrl)
    {
       //Data
        $consumerData = $this->loadData('generic_consumer');
        //Preconditions
        $this->oauthHelper()->createConsumer($consumerData);
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Steps
        //Open consumer
        $this->addParameter('consumer_search_name', $consumerData['consumer_name']);
        $this->oauthHelper()->openConsumer(array('name' => $consumerData['consumer_name']));
        //Fill Rejected Callback URL field with invalid data
        $this->oauthHelper()->editConsumer(array('rejected_callback_url' => $wrongUrl));
        // Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Verify message
        $xpath = $this->oauthHelper()->getUIMapFieldXpath('new_consumer', 'rejected_callback_url');
        $this->addParameter('rejectedCallbackUrl', $xpath);
        $this->assertMessagePresent('error', 'invalid_rejected_callback_url');
        $this->assertTrue($this->checkCurrentPage('edit_consumer'), $this->getParsedMessages());
        //Verify value of rejected_callback_url field
        $this->clickButton('reset');
        $this->assertEquals($consumerData['callback_url'], $this->oauthHelper()->getFieldValue('callback_url'),
            'Rejected Callback Url does not match.');
    }

    /**
     * <p>Edit consumer with invalid value for 'Callback Url' field</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1.Search and open consumer.</p>
     * <p>2. Fill Callback URL with "invalid url" text.</p>
     * <p>3. Click 'Save' button.</p>
     * <p> Expected result:</p>
     * <p> Message "Please enter a valid URL. Protocol is required (http://, https:// or ftp://)"
     * appears under 'Callback Url' field. Edit Consumer page is opened.</p>
     * <p>3. Click 'Reset' button.</p>
     * <p> Expected result:</p>
     * <p> Verify that Callback URL value does not change</p>
     *
     * @param string $wrongUrl
     * @depends withAllValidData
     * @dataProvider withInvalidUrlDataProvider
     * @test
     */
    public function withInvalidCallbackURL($wrongUrl)
    {
       //Data
        $consumerData = $this->loadData('generic_consumer');
        //Preconditions
        $this->oauthHelper()->createConsumer($consumerData);
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Steps
        //Open consumer
        $this->addParameter('consumer_search_name', $consumerData['consumer_name']);
        $this->oauthHelper()->openConsumer(array('name' => $consumerData['consumer_name']));
        //Fill Callback URL field with invalid data
        $this->oauthHelper()->editConsumer(array('callback_url' => $wrongUrl));
        // Saving consumer name for tearDown
        $this->_consumerToBeDeleted = $consumerData['consumer_name'];
        //Verify message
        $xpath = $this->oauthHelper()->getUIMapFieldXpath('new_consumer', 'callback_url');
        $this->addParameter('callbackUrl', $xpath);
        $this->assertMessagePresent('error', 'invalid_callback_url');
        $this->assertTrue($this->checkCurrentPage('edit_consumer'), $this->getParsedMessages());
        //Verify value of callback_url field
        $this->clickButton('reset');
        $this->assertEquals($consumerData['callback_url'], $this->oauthHelper()->getFieldValue('callback_url'),
            'Callback Url does not match.');
    }

    public function withInvalidUrlDataProvider()
    {
        return array(
            array('invalid'),
            array('www.localhost.com')
        );
    }
}
