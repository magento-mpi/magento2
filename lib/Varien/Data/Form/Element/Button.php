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
 * Form button element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Button extends Varien_Data_Form_Element_Abstract
{
    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('data-mage-init');

    /**
     * @param array $attributes
     */
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('button');
        $this->setExtType('textfield');
    }

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
