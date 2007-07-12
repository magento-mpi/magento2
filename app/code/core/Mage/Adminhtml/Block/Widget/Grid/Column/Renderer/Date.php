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
    
	protected function _getFormat()
	{
	    $format = $this->getColumn()->getFormat();
	    if (!$format) {
            if (is_null(self::$_format)) {
				if (!(self::$_format = Mage::getStoreConfig('general/local/date_format'))) {
				    self::$_format = '%a, %b %e %Y';
				}
			}
			$format = self::$_format;
	    }
	    return $format;
	}
	
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
			$format = $this->_getFormat();
        	if (false === strstr($format, '%')) {
        	    return date($format, strtotime($data));
        	}
            return strftime($format, strtotime($data));
        }
        return $this->getColumn()->getDefault();
    }
}