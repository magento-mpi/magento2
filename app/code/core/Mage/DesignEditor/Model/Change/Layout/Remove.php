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
 * Layout remove change model
 */
class Mage_DesignEditor_Model_Change_Layout_Remove extends Mage_DesignEditor_Model_Change_Layout
{
    /**
     * Layout directive associated with this change
     */
    const LAYOUT_DIRECTIVE   = 'remove';

    /**
     * Get data to render layout update directive
     *
     * @return array
     */
    public function getLayoutUpdateData()
    {
        return array(
            'name'         => $this->getData('element_name'),
        );
    }
}
