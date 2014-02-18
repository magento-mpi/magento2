<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Plugin;

use Magento\Code\Plugin\InvocationChain;

class ThemeCopyService
{
    /**
     * @var \Magento\DesignEditor\Model\Theme\ChangeFactory
     */
    protected $_themeChangeFactory;

    /**
     * @param \Magento\DesignEditor\Model\Theme\ChangeFactory $themeChangeFactory
     */
    public function __construct(\Magento\DesignEditor\Model\Theme\ChangeFactory $themeChangeFactory)
    {
        $this->_themeChangeFactory = $themeChangeFactory;
    }

    /**
     * Copy additional information about theme change time
     *
     * @param \Magento\Theme\Model\CopyService $subject
     * @param callable $proceed
     * @param \Magento\View\Design\ThemeInterface $source
     * @param \Magento\View\Design\ThemeInterface $target
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCopy(
        \Magento\Theme\Model\CopyService $subject,
        \Closure $proceed,
        \Magento\View\Design\ThemeInterface $source,
        \Magento\View\Design\ThemeInterface $target
    ) {
        $proceed($source, $target);
        if ($source && $target) {
            /** @var $sourceChange \Magento\DesignEditor\Model\Theme\Change */
            $sourceChange = $this->_themeChangeFactory->create();
            $sourceChange->loadByThemeId($source->getId());
            /** @var $targetChange \Magento\DesignEditor\Model\Theme\Change */
            $targetChange = $this->_themeChangeFactory->create();;
            $targetChange->loadByThemeId($target->getId());

            if ($sourceChange->getId()) {
                $targetChange->setThemeId($target->getId());
                $targetChange->setChangeTime($sourceChange->getChangeTime());
                $targetChange->save();
            } elseif ($targetChange->getId()) {
                $targetChange->delete();
            }
        }
    }
}
