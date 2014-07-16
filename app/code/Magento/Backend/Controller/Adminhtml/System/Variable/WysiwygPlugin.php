<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class WysiwygPlugin extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * WYSIWYG Plugin Action
     *
     * @return void
     */
    public function execute()
    {
        $customVariables = $this->_objectManager->create('Magento\Core\Model\Variable')->getVariablesOptionArray(true);
        $storeContactVariabls = $this->_objectManager->create(
            'Magento\Email\Model\Source\Variables'
        )->toOptionArray(
            true
        );
        $variables = array($storeContactVariabls, $customVariables);
        $this->getResponse()->representJson(\Zend_Json::encode($variables));
    }
}
