<?php
/**
 * Adminhtml grid item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Format variables pattern
     *
     * @var string
     */
    protected $_variablePattern = '/\\$([a-z0-9_]+)/i';

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @param string $index
     * @param string $format
     */
    public function render(Varien_Object $row)
    {
        $index  = $this->getColumn()->getIndex();
        $format = $this->getColumn()->getFormat();
        $defaultValue = $this->getColumn()->getDefault();

        if (is_null($format)) {
            // If no format and it column not filtered specified return data as is.
            $string = ($row->getData($index)) ? $row->getData($index) : $defaultValue;
            return htmlspecialchars($string);
        }
        elseif (preg_match_all($this->_variablePattern, $format, $matches)) {
            // Parsing of format string
            $formatedString = $format;
            foreach ($matches[0] as $matchIndex=>$match) {
                $value = $row->getData($matches[1][$matchIndex]);
                $formatedString = str_replace($match, $value, $formatedString);
            }
            return $formatedString;
        } else {
            return '(wrong_format)';
        }
    }
}