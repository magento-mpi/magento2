<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

class FileProvider implements \Magento\Framework\View\Design\Theme\FileProviderInterface
{
    /**
     * @var \Magento\Core\Model\Resource\Theme\File\CollectionFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Core\Model\Resource\Theme\File\CollectionFactory $fileFactory
     */
    public function __construct(\Magento\Core\Model\Resource\Theme\File\CollectionFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(\Magento\Framework\View\Design\ThemeInterface $theme, array $filters = array())
    {
        /** @var \Magento\Framework\View\Design\Theme\File\CollectionInterface $themeFiles */
        $themeFiles = $this->fileFactory->create();
        $themeFiles->addThemeFilter($theme);
        foreach ($filters as $field => $value) {
            $themeFiles->addFieldToFilter($field, $value);
        }
        $themeFiles->setDefaultOrder();
        return $themeFiles->getItems();
    }
}
