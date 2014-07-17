<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools;

use Magento\Framework\Model\Exception as CoreException;

class SaveQuickStyles extends \Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools
{
    /**
     * Save quick styles data
     *
     * @return void
     */
    public function execute()
    {
        $controlId = $this->getRequest()->getParam('id');
        $controlValue = $this->getRequest()->getParam('value');
        try {
            $themeContext = $this->_initContext();
            /** @var $configFactory \Magento\DesignEditor\Model\Editor\Tools\Controls\Factory */
            $configFactory = $this->_objectManager->create('Magento\DesignEditor\Model\Editor\Tools\Controls\Factory');
            $configuration = $configFactory->create(
                \Magento\DesignEditor\Model\Editor\Tools\Controls\Factory::TYPE_QUICK_STYLES,
                $themeContext->getStagingTheme(),
                $themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($controlId => $controlValue));
            $response = array('success' => true);
        } catch (CoreException $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        } catch (\Exception $e) {
            $errorMessage = __(
                'Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).'
            );
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response)
        );
    }
}
