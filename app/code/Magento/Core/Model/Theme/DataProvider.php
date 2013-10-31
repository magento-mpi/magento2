<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

class DataProvider implements \Magento\View\Design\Theme\DataProviderInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getByFullPath($fullPath)
    {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = $this->objectManager->create('Magento\Core\Model\Resource\Theme\Collection');
        return $themeCollection->getThemeByFullPath($fullPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($themeId)
    {
        /** @var $themeModel \Magento\View\Design\ThemeInterface */
        $themeModel = $this->objectManager->create('Magento\View\Design\ThemeInterface');
        return $themeModel->load($themeId);
    }
}
