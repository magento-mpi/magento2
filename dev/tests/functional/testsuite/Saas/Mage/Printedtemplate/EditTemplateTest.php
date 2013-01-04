<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Mage_Printedtemplate_EditTemplateTest
    extends Mage_Selenium_TestCase
{
    /**
     * @test
     * @param string $templateType
     * @dataProvider templateTypeDataProvider
     * Steps:
     * 1. Create template
     * 2. Open created template
     * 3. Change template name
     * 3. Save and check in templates grid
     */
    public function templateEditingTest($templateType)
    {
        $this->loginAdminUser();
        //create template
        $templateName = $this->printedtemplateHelper()->createTemplateFromSample($templateType);

        //search created template in templates grid
        $this->addParameter('templateName', $templateName);
        $this->printedtemplateHelper()->searchTemplateByName($templateName);
        $this->setCurrentPage('printed_templates');
        $this->clickControl('pageelement', 'grid_item');

        //edit template name
        $this->setCurrentPage('edit_printed_templates');
        $this->addParameter('id', '\\/d');
        $newTemplate = $this->loadDataSet(
            'TemplatesData', 'template_name',
            array(
                'template_name' => '%randomize% template NEW' . $templateType
            )
        );
        $this->fillForm($newTemplate);
        $this->clickButton('save_template');

        //search new template by new name
        $this->printedtemplateHelper()->searchTemplateByName($newTemplate['template_name']);
        $this->logoutAdminUser();
    }

    /**
     * Data provider for templateEditingTest
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
