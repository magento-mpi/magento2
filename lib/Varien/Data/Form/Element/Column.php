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
 * Form column
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Column extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->setType('column');
    }
}