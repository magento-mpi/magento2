<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration\Edit;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Controller\Adminhtml\Integration;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                )
            )
        );
        $integrationData = $this->_coreRegistry->registry(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        if (isset($integrationData[Info::DATA_ID])) {
            $form->addField(Info::DATA_ID, 'hidden', array('name' => 'id'));
            $form->setValues($integrationData);
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
