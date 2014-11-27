<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\DataProvider\Page\Options;

use Magento\Ui\Component\Listing\OptionsInterface;
use Magento\Core\Model\PageLayout\Config\Builder;

/**
 * Class PageLayout
 */
class PageLayout implements OptionsInterface
{
    /**
     * @var \Magento\Core\Model\PageLayout\Config\Builder
     */
    protected $pageLayoutBuilder;

    /**
     * Constructor
     *
     * @param Builder $pageLayoutBuilder
     */
    public function __construct(Builder $pageLayoutBuilder)
    {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
    }

    /**
     * Get options
     *
     * @param array $options
     * @return array
     */
    public function getOptions(array $options = [])
    {
        $newOptions = $this->pageLayoutBuilder->getPageLayoutsConfig()->getOptions();
        foreach ($newOptions as $key => $value) {
            $newOptions[$key] = [
                'label' => $value,
                'value' => $key
            ];
        }

        return array_merge_recursive($newOptions, $options);
    }
}
