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

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Date format string
	 */
	protected static $_format = null;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
					if (is_null(self::$_format)) {
						if (!(self::$_format = Mage::getSingleton('core/store')->getConfig('core/date_format'))) self::$_format = '%a, %b %e %Y';
					}
        	if (false === strstr(self::$_format, '%')) return date(self::$_format, strtotime($data));
            return strftime(self::$_format, strtotime($data));
        }
        return null;
    }
}