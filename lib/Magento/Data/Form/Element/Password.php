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
 * Form password element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Password extends \Magento\Data\Form\Element\AbstractElement
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('password');
        $this->setExtType('textfield');
    }
    
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }
}
