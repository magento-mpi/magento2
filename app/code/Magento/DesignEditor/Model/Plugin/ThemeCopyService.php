<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Plugin;

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
     * @param array $methodArguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     */
    public function aroundCopy(array $methodArguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $invocationChain->proceed($methodArguments);

        /** @var $sourceTheme \Magento\Core\Model\Theme|null */
        /** @var $targetTheme \Magento\Core\Model\Theme|null */
        list($sourceTheme, $targetTheme) = $methodArguments;
        if ($sourceTheme && $targetTheme) {
            /** @var $sourceChange \Magento\DesignEditor\Model\Theme\Change */
            $sourceChange = $this->_themeChangeFactory->create();
            $sourceChange->loadByThemeId($sourceTheme->getId());
            /** @var $targetChange \Magento\DesignEditor\Model\Theme\Change */
            $targetChange = $this->_themeChangeFactory->create();;
            $targetChange->loadByThemeId($targetTheme->getId());

            if ($sourceChange->getId()) {
                $targetChange->setThemeId($targetTheme->getId());
                $targetChange->setChangeTime($sourceChange->getChangeTime());
                $targetChange->save();
            } elseif ($targetChange->getId()) {
                $targetChange->delete();
            }
        }
    }
}
