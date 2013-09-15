<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend grid widget massaction item additional action default
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional;

class DefaultAdditional
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\AdditionalInterface
{

    public function createFromConfiguration(array $configuration)
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        foreach ($configuration as $itemId=>$item) {
            $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }

}
