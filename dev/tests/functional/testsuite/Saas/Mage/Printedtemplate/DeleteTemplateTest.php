<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Mage_Printedtemplate_DeleteTemplateTest
    extends Mage_Selenium_TestCase
{
    /**
     * @test
     * @param string $templateType
     * @dataProvider templateTypeDataProvider
     * Steps:
     * 1. Create template
     * 2. Open created template
     * 3. Delete it
     * 4. Check that it is absent in templates grid
     */
    public function deleteTemplateTest($templateType)
    {
        $this->loginAdminUser();
        //create template
        $templateName = $this->printedtemplateHelper()->createTemplateFromSample($templateType);

        //search created template in templates grid
        $this->addParameter('templateName', $templateName);
        $this->PrintedtemplateHelper()->searchTemplateByName($templateName);
        $this->setCurrentPage('printed_templates');
        $this->clickControl('pageelement', 'grid_item');

        //open template
        $this->setCurrentPage('edit_printed_templates');
        $this->addParameter('id', '\\/d');
        //delete template
        $this->clickButtonAndConfirm('delete_template', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_template');

        //seach deleted template by new name
        $this->printedtemplateHelper()->searchTemplateByName($templateName, false);

        $this->logoutAdminUser();
    }

    /**
     * Data provider for deleteTemplateTest
     *
     * @return array
     */
    public function templateTypeDataProvider()
    {
        return array(
            array('Invoice'),
            array('Credit Memo'),
            array('Shipment')
        );
    }
}
