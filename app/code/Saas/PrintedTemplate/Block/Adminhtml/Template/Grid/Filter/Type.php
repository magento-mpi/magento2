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
 * Adminhtml system template grid type filter
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Filter_Type extends
    Magento_Backend_Block_Widget_Grid_Column_Filter_Select
{

    /**
     * Prepares available values for field type
     *
     * @return array
     */
    protected function _getOptions()
    {
        $result = array();
        $result[] = array('value' => null, 'label' => null);
        $types = Mage::getSingleton('Saas_PrintedTemplate_Model_Source_Type')->getAllOptions();
        foreach ($types as $code => $label) {
            $result[] = array('value' => $code, 'label' => $this->__($label));
        }

        return $result;
    }

    /**
     * Prepares condition for filtering by type
     *
     * @return array
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}
