<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form column
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Column extends Magento_Data_Form_Element_Abstract 
{
    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->setType('column');
    }
}