<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Item Dynamic attributes Form Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block;

class Form extends \Magento\CustomAttribute\Block\Form
{
    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'magento_rma_item_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Magento\Rma\Model\Item\Form';
}
