<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Mage_Printedtemplate_LoadTemplateTest
    extends Mage_Selenium_TestCase
{

    /**
     * @test
     * @dataProvider loadTemplateDataProvider
     * Steps:
     * 1. Start to Create template
     * 2. Load one by one sample templates
     * 3. Check that templates are loaded.
     */
    public function loadTemplateTest($templateType, $sampleTemplateData)
    {
        $this->loginAdminUser();
        //Start to create template
        $this->printedtemplateHelper()->openTemplateEditForm($templateType);

        foreach ($sampleTemplateData as $sampleTemplate => $originalTemplateName) {

            //load sample template
            $this->addParameter('templateType', $templateType);
            $loadTemplateData = $this->loadDataSet(
                'TemplatesData', 'load_sample_template',
                array(
                    'template' => "Printed $templateType",
                    'sample_template' => $sampleTemplate)
            );

            $this->fillForm($loadTemplateData);
            $this->clickButton('load_template', false);
            $this->waitForAjax();

            //Get value of Template Name field
            $actualTemplateName = $this->getElementsValue($this->_getControlXpath('field', 'template_name'), 'value');
            //Check that Template name is the same as expected
            var_dump($actualTemplateName[0]);
            if ($actualTemplateName[0] != $originalTemplateName) {
                $this->fail('Template is not loaded or has incorrect name');
            }
        }
        $this->logoutAdminUser();
    }

    /**
     * Data provider for loadTemplateTest
     *
     * @return array
     */
    public function loadTemplateDataProvider()
    {
        return array(
            //Data Set #0: Invoice
            array('Invoice',
                array(
                    'Magento Standard' => 'Invoice template',
                    'French Standard' => 'Modèle de facture Française',
                    'UK Standard' => 'Invoice template',
                    'German Standard' => 'Rechnung'
                )
            ),

            //Data Set #2: Shipment (Delivery)
            array('Shipment',
                array(
                    'Magento Standard' => 'Shipment US template',
                    'French Standard' => 'Modèle de bon de livraison',
                    'UK Standard' => 'Shipment template',
                    'German Standard' => 'Lieferung'
                )
            ),

            //Data Set #1: Credit Memo
            array('Credit Memo',
                array(
                    'Magento Standard' => 'Credit Memo US Template',
                    'French Standard' => 'Modèle d\'avoir Français',
                    'UK Standard' => 'Credit memo template',
                    'German Standard' => 'Gutschrift'
                )
            )
        );
    }
}
