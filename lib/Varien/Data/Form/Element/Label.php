<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Data form abstract class
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Label extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml()
    {
    	$html = $this->getBold() ? '<strong>' : '';
    	$html.= $this->getEscapedValue();
    	$html.= $this->getBold() ? '</strong>' : '';
    	$html.= $this->getAfterElementHtml();
    	return $html;
    }

}