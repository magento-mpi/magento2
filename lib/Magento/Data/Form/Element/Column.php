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
namespace Magento\Data\Form\Element;

class Column extends \Magento\Data\Form\Element\AbstractElement
{
    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->setType('column');
    }
}
