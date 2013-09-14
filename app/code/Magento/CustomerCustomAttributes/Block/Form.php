<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Dynamic attributes Form Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block;

class Form extends \Magento\CustomAttribute\Block\Form
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Magento\Customer\Model\Form';

}
