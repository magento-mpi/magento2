<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

class DownloadCustomCss extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * Download custom css file
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        try {
            /** @var $themeFactory \Magento\Framework\View\Design\Theme\FlyweightFactory */
            $themeFactory = $this->_objectManager->create('Magento\Framework\View\Design\Theme\FlyweightFactory');
            $theme = $themeFactory->create($themeId);
            if (!$theme) {
                throw new \InvalidArgumentException(sprintf('We cannot find a theme with id "%1".', $themeId));
            }

            $customCssFiles = $theme->getCustomization()->getFilesByType(
                \Magento\Theme\Model\Theme\Customization\File\CustomCss::TYPE
            );
            /** @var $customCssFile \Magento\Framework\View\Design\Theme\FileInterface */
            $customCssFile = reset($customCssFiles);
            if ($customCssFile && $customCssFile->getContent()) {
                return $this->_fileFactory->create(
                    $customCssFile->getFileName(),
                    array('type' => 'filename', 'value' => $customCssFile->getFullPath()),
                    DirectoryList::ROOT_DIR
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We cannot find file'));
            $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
