<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Renderer;

/**
 * Form fieldset default renderer
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Fieldset
    extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
{
    /**
     * @var string
     */
    protected $_template = 'edit/item/renderer/fieldset.phtml';
}
