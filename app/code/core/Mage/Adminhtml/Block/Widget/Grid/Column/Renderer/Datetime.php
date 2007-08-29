<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer datetime
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka  <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
                try {
                    self::$_format = Mage::getStoreConfig('general/local/datetime_format_medium');
                }
                catch (Exception $e) {
                }
				if (!(self::$_format)) {
				    self::$_format = '%a, %b %e %Y [%I:%M %p]';
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