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
 * oAuth consumer Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     {license_link}
 */
class Oauth_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create consumer.
     *
     * PreConditions: 'OAuth Consumers' page is opened.
     * @param array $userData
     */
    public function createConsumer(array $consumerData)
    {
        //Click 'Add New' button.
        $this->clickButton('add_new_consumer');
        //Fill all data
        $this->fillForm($consumerData);
        $this->saveForm('save_consumer');
    }

    /**
     * Open consumer.
     *
     * PreConditions: 'oAuth Consumers' page is opened.
     * @param array $searchData
     */
    public function openConsumer(array $searchData)
    {
        $this->assertTrue($this->searchAndOpen($searchData, true, 'oauth_consumers'), 'Consumer is not found');
    }

    /**
     * Edit consumer.
     *
     * PreConditions: Edit Consumer page is opened.
     * @param array $newConsumerData,
     * @param array $searchData
     */
    public function editConsumer(array $newConsumerData)
    {
        //Fill all data
        $this->fillForm($newConsumerData);
        $this->saveForm('save_consumer');
    }

    /**
     * Method finds field value using xPath from UIMap
     *
     * @param string $fieldName
     * @return string $value
     */
    public function getFieldValue($fieldName)
    {
        $UIMap = $this->getUimapPage('admin', 'edit_consumer')->getMainForm()->getElements();
        $consumerInformation = $UIMap['fieldsets']->getFieldset('consumer_information')->getElements();
        $xpath = $consumerInformation['fields']->get($fieldName);
        return $this->getElementByXpath($xpath, 'value');
    }

    /**
     * Method finds xPath of searchable element from UIMap
     * for example getUIMapFieldXpath('edit_consumer', 'consumer_name') returns //input[@id='name']
     * @param string $pageName
     * @param string $fieldName
     * @return string
     */
    public function getUIMapFieldXpath($pageName, $fieldName)
    {
        $UIMap = $this->getUimapPage('admin', $pageName)->getMainForm()->getElements();
        $consumerInformation = $UIMap['fieldsets']->getFieldset('consumer_information')->getElements();
        return $consumerInformation['fields']->get($fieldName);
    }

    /**
     *Method finds Consumer by name and deletes them.
     *
     * @param string $consumerToBeDeleted
     */
    public function deleteConsumerByName($consumerToBeDeleted)
    {
        //Steps
        $this->navigate('oauth_consumers');
        $this->addParameter('consumer_search_name', $consumerToBeDeleted);
        $this->oauthHelper()->openConsumer(array('name' => $consumerToBeDeleted));
        $this->clickButtonAndConfirm('delete_consumer', 'confirmation_for_delete');
    }
}
