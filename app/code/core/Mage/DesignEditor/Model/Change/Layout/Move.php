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
class Mage_DesignEditor_Model_Change_Layout_Move extends Mage_DesignEditor_Model_Change_LayoutAbstract
{
    /**
     * Layout directive associated with this change
     */
    const LAYOUT_DIRECTIVE_MOVE = 'move';

    /**
     * Get required data fields for move layout change
     *
     * @return array
     */
    protected function _getRequiredFields()
    {
        $requiredFields = parent::_getRequiredFields();
        $requiredFields[] = 'destination_container';
        $requiredFields[] = 'destination_order';
        $requiredFields[] = 'origin_container';
        $requiredFields[] = 'origin_order';

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
            'element'     => $this->getData('element_name'),
            'after'       => $this->getData('destination_order'),
            'destination' => $this->getData('destination_container')
        );
    }

    /**
     * Get layout update directive for given layout change
     *
     * @return string
     */
    public function getLayoutDirective()
    {
        return self::LAYOUT_DIRECTIVE_MOVE;
    }
}
