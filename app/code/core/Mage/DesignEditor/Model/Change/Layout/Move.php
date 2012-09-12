<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout move change model
 */
class Mage_DesignEditor_Model_Change_Layout_Move extends Mage_DesignEditor_Model_Change_Layout
{
    /**
     * Layout directive associated with this change
     */
    const LAYOUT_DIRECTIVE = 'move';

    /**
     * Get required data fields for move layout change
     *
     * @return array
     */
    protected function _getRequiredFields()
    {
        $requiredFields = parent::_getRequiredFields();
        $requiredFields[] = 'container';
        $requiredFields[] = 'after';

        return $requiredFields;
    }

    /**
     * Get data to render layout update directive
     *
     * @return array
     */
    public function getLayoutUpdateData()
    {
        return array(
            'name'         => $this->getData('element_name'),
            'element_name' => $this->getData('element_name'),
            'destination'  => $this->getData('destination')
        );
    }
}
