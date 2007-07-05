<?php
/**
 * Adminhtml grid item renderer date
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka  <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Renderer_Date extends Mage_Core_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface
{
		/**
		 * Date format string
		 */
		protected static $_format = null;

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @param Varien_Object $column
     */
    public function render(Varien_Object $row, Varien_Object $column)
    {
        if ($data = $row->getData($column->getIndex())) {
					if (is_null(self::$_format)) {
						if (!(self::$_format = Mage::getSingleton('core/store')->getConfig('core/date_format'))) self::$_format = '%a, %b %e %Y';
					}
        	if (false === strstr(self::$_format, '%')) return date(self::$_format, strtotime($data));
            return strftime(self::$_format, strtotime($data));
        }
        return null;
    }
}