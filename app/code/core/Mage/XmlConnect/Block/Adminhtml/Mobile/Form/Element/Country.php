<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect Country selector form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Country
    extends Varien_Data_Form_Element_Checkboxes
{
    /**
     * Flag of using the border in the table's TD
     *
     * @var bool
     */
    protected $_useBorderClass = false;

    /**
     * Init Element
     *
     * @param array $attributes
     */
    protected function _construct($attributes=array())
    {
        parent::_construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('country');
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();

        if (empty($values)) {
            return '';
        }

        $columns = (int)$this->getData('columns');
        $columns = $columns ? $columns : 1;
        $rows = ceil(count($values) / $columns);
        $row = $column = 0;

        $options = array();

        foreach ($values as $value) {
            if (empty($value['value'])) {
                continue;
            }
            $options[$row++][$column] = $value;
            if ($row == $rows) {
                $row = 0;
                $column++;
            }
        }

        while ($row < $rows) {
            $options[$row++][$column] = '';
        }

        $id = $this->getData('id');
        $id = empty($id) ? '' : ' id="' . $id . '-table"';
        $class = $this->getData('class');
        $html = PHP_EOL . "<table class=\"countries {$class}\"{$id}>" . PHP_EOL;

        $zebrine = '';
        $stripy = false;
        if (strpos($class, 'stripy')) {
            $stripy = true;
        }

        $columns--;
        foreach ($options as $row) {
            $html .= "<tr{$zebrine}>" . PHP_EOL;

            if ($stripy) {
                $zebrine = empty($zebrine) ? ' class="odd"' : '';
                $this->_useBorderClass = true;
                foreach ($row as $idx => $option) {
                    /**
                     * for istore (as shown by $stripy) use border settings in TD
                     */
                    if ($idx == $columns) {
                        /**
                         * for last table's column TD should not have a border
                         */
                        $this->_useBorderClass = false;
                    }
                    $html .= $this->_optionToHtml($option);
                }
            } else {
                foreach ($row as $option) {
                    $html .= $this->_optionToHtml($option);
                }
            }

            $html .= PHP_EOL . '</tr>' . PHP_EOL;
        }

        $html .= '</table>' . PHP_EOL . $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get HTML code for the one option
     *
     * @param array $option
     * @return string
     */
    protected function _optionToHtml($option)
    {
        if (empty($option)) {
            $html = '<td>&nbsp;</td><td>&nbsp;</td>';
        } else {
            $id = $this->getHtmlId() . '_' . $this->_escape($option['value']);
            $isNameLeft = $this->getData('place_name_left');

            $border = $this->_useBorderClass ? ' class="border"' : '';
            $html = '<td' . $border . '><input id="' . $id . '"';
            foreach ($this->getHtmlAttributes() as $attribute) {
                $value = $this->getDataUsingMethod($attribute, $option['value']);
                if ($value) {
                    $html .= ' ' . $attribute . '="' . $value . '"';
                }
            }
            $html .= ' value="' . $option['value'] . '" /></td>';

            $label = '<td><label for="' . $id . '" style="white-space: nowrap;">' . $option['label'] . '</label></td>';

            if ($isNameLeft) {
                $html = $label . $html;
            } else {
                $html = $html . $label;
            }
        }

        return $html;
    }
}
