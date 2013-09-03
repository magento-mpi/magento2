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
 * Form button element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Button extends \Magento\Data\Form\Element\AbstractElement
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
