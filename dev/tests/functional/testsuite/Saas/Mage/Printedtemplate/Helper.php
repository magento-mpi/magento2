<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Printedtemplate_Helper extends Mage_Selenium_AbstractHelper
{
    /*
    * Selects template type during creation and opens Edit form for initial editing
    *
    * @param string $templateType
    */
    public function openTemplateEditForm($templateType = 'Invoice')
    {
        //navigate to templates grid
        $this->navigate('printed_templates');

        //create template
        $this->clickButton('add_new_template');
        $this->addParameter('templateType', $templateType);
        $loadTemplateData = $this->loadDataSet(
            'TemplatesData', 'template_type',
            array('template_type' => $templateType)
        );
        $this->fillForm($loadTemplateData);
        $this->clickButton('continue');
    }

    /*
    * Creates template based on loaded sample template
    *
    * @param string $templateType
    * @param string $sampleTemplate
    * @return $templateName
    */
    public function createTemplateFromSample($templateType = 'Invoice', $sampleTemplate = 'Magento Standard')
    {
        $this->printedtemplateHelper()->openTemplateEditForm($templateType);

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

        //change template name and save
        $templateName = $this->loadDataSet(
            'TemplatesData', 'template_name',
            array(
                'template_name' => '%randomize% template ' . $templateType,
            )
        );
        $this->fillForm($templateName);
        $this->clickButton('save_template',false);
        $this->waitForAjax();
        $this->waitForPageToLoad();
        $this->setCurrentPage('printed_templates');
        $this->elementIsPresent('success_saved_template');

        return $templateName['template_name'];
    }

    /*
    * Searchs a template in templates grid by name
    * @param string $templateName
    * @param bool $expectedResult
    */
    public function searchTemplateByName($templateName, $expectedResult = true)
    {
        $this->navigate('printed_templates');
        //search in templates grid
        $searchData = array('filter_template_name' => $templateName);
        $this->fillForm($searchData);
        $this->clickButton('search');
        $this->addParameter('templateName', $templateName);
        $xPath = $this->_getControlXpath('pageelement', 'grid_item');
        $availableElement = $this->elementIsPresent($xPath);

        if ($expectedResult == false) {
            if ($availableElement ) {
                if ($availableElement->displayed()) {
                    $this->fail($this->locationToString() . "Template is present in templates grid, but it shouldn't.");
                }
            }
        }
        else {
            if (!$availableElement || !$availableElement->displayed()) {
                $this->fail($this->locationToString() . "Template is not found in templates grid.");
            }
        }
    }
}
