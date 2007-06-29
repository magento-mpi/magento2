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
 
class Mage_Adminhtml_Block_Widget_Grid_Renderer extends Varien_Filter_Object implements Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface
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
    public function render(Varien_Object $row, $index, $format=null)
    {
        $array = $this->filter($row);
        
        if (is_null($format) && !isset($array[$index])) {
            // If no format and it column not filtered specified return data as is.
            return $row->getData($index);
        } else if (is_null($format)) {
            // If no format specified return filtered data.
            return $array[$index];
        } else if (preg_match_all($this->_variablePattern, $format, $matches)) {
            // Parsing of format string
            $formatedString = $format;            
            foreach ($matches[0] as $matchIndex=>$match) {
                $value = '';
                if(isset($array[$matches[1][$matchIndex]])) {
                    $value = $array[$matches[1][$matchIndex]];
                }
                
                $formatedString = str_replace($match, $value, $formatedString);
            }
            return $formatedString;
        } else {
            return '(wrong_format)';
        }
    }
}