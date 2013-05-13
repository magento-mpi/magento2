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
class Saas_Mage_ProductAttribute_Helper extends Core_Mage_ProductAttribute_Helper
{
    /**
     * Delete attributes
     * @param array
     */
    public function deleteAttributes(array $attributesData)
    {
        foreach($attributesData as $attrData) {
            $searchData = $this->loadDataSet('ProductAttributes', 'attribute_search_data',
                array(
                    'attribute_code'  => $attrData['attribute_code'],
                    'attribute_label' => $attrData['attribute_label'],
                )
            );
            $this->openAttribute($searchData);
            $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
            //Verifying
            $this->assertMessagePresent('success', 'success_deleted_attribute');
        }
    }
}