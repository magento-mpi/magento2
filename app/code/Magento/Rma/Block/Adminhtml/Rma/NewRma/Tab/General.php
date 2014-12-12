<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * General Tab in New RMA form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab;

class General extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setForm($form);
        return $this;
    }
}
