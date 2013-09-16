<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_DesignEditor_Model_Plugin_ThemeCopyService
{
    /**
     * @var Magento_DesignEditor_Model_Theme_ChangeFactory
     */
    protected $_themeChangeFactory;

    /**
     * @param Magento_DesignEditor_Model_Theme_ChangeFactory $themeChangeFactory
     */
    public function __construct(Magento_DesignEditor_Model_Theme_ChangeFactory $themeChangeFactory)
    {
        $this->_themeChangeFactory = $themeChangeFactory;
    }

    /**
     * Copy additional information about theme change time
     *
     * @param array $methodArguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     */
    public function aroundCopy(array $methodArguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        $invocationChain->proceed($methodArguments);

        /** @var $sourceTheme Magento_Core_Model_Theme|null */
        /** @var $targetTheme Magento_Core_Model_Theme|null */
        list($sourceTheme, $targetTheme) = $methodArguments;
        if ($sourceTheme && $targetTheme) {
            /** @var $sourceChange Magento_DesignEditor_Model_Theme_Change */
            $sourceChange = $this->_themeChangeFactory->create();
            $sourceChange->loadByThemeId($sourceTheme->getId());
            /** @var $targetChange Magento_DesignEditor_Model_Theme_Change */
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
