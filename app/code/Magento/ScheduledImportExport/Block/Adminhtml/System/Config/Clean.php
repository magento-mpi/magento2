<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Clean now import/export file history button renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\System\Config;

class Clean extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * Remove scope label
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $url = $this->getUrl(
            'adminhtml/scheduled_operation/logClean',
            ['section' => $this->getRequest()->getParam('section')]
        );
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['id' => 'clean_now', 'label' => __('Clean Now'), 'onclick' => 'setLocation(\'' . $url . '\')']
        );

        return $button->toHtml();
    }
}
