<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Mage_Printedtemplate_TemplateCreationTest
    extends Mage_Selenium_TestCase
{
    /**
     * @test
     * @dataProvider templateCreationDataProvider
     * @param $templateType
     * Steps:
     * 1. Create template
     * 2. Check it on templates grid
     * 3. Check visibility of new templates on System - Configuration
     */
    public function templateCreationTest($templateType)
    {
        $this->loginAdminUser();

        //create template
        $templateName = $this->printedtemplateHelper()->createTemplateFromSample($templateType);

        //search in templates grid
        $this->printedtemplateHelper()->searchTemplateByName($templateName);

        $this->navigate('system_configuration');
        $this->openTab('sales_pdf_print_outs');
        $this->waitForPageToLoad();

        //set dropdown to search in depending on template type
        switch ($templateType) {
            case "Invoice":
                $templateDropdown = 'invoice_printed_template';
                break;

            case "Credit Memo":
                $templateDropdown = 'credit_memo_printed_template';
                break;

            case "Shipment":
                $templateDropdown = 'shipment_printed_template';
                break;
        }

        //check if template present in configuration drop-down
        $xPath = $this->_getControlXpath('dropdown', $templateDropdown);
        $existentTemplates = $this->getElements($xPath);//getElementsByXpath($xPath);
        $this->assertContains(
            $templateName, $existentTemplates[0],
            'Created template is absent in available templates list.'
        );

        $this->logoutAdminUser();
    }

    /**
     * Data provider for templateCreationTest
     *
     * @return array
     */
    public function templateCreationDataProvider()
    {
        return array(
            array('Invoice'),
//            array('Credit Memo'),
//            array('Shipment')
        );
    }
}
