<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Massaction\Item\Additional;

/**
 * Backend grid widget massaction item additional action default
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class DefaultAdditional extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Listing\Block\Massaction\Item\Additional\AdditionalInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromConfiguration(array $configuration)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        foreach ($configuration as $itemId => $item) {
            $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }
}
