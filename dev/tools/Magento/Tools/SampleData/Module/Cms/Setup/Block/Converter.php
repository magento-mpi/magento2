<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Cms\Setup\Block;

/**
 * Class Converter
 */
class Converter
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Category\CategoryLoader
     */
    protected $categoryLoader;

    /**
     * @var \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
     */
    protected $productConverter;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory,
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader,
        \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter $productConverter
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->eavConfig = $eavConfig;
        $this->categoryLoader = $categoryLoader;
        $this->productConverter = $productConverter;
    }

    /**
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($row as $field => $value) {
            if ('category_path' == $field) {
                $categoryId = $this->getCategoryId($this->getArrayValue($value));
                $data['category_id'] = $categoryId;
                continue;
            }
            if ('content' == $field) {
                $data['block'][$field] = $this->convertContentUrls($value);
                continue;
            }
            $data['block'][$field] = $value;
        }
         return $data;
    }

    /**
     * @param $urlKey
     * @return mixed
     * @throws \Magento\Framework\Model\Exception
     */
    protected function getCategoryId($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->getFirstItem();
        return $category->getId();
    }

    /**
     * Get formatted array value
     *
     * @param mixed $value
     * @param string $separator
     * @return array
     */
    protected function getArrayValue($value, $separator = "/")
    {
        if (is_array($value)) {
            return $value;
        }
        if (false !== strpos($value, $separator)) {
            $value = array_filter(explode($separator, $value));
        }
        return !is_array($value) ? [$value] : $value;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function convertContentUrls($content)
    {
        $categoryReplacement = $this->getCategoriesMatches($content);
        if (!empty($categoryReplacement['path'])) {
            $categoriesUrls = $this->getCategoriesUrl($categoryReplacement);
            foreach ($categoriesUrls as $categoryPath => $categoryUrl) {
                $content = $this->replaceContentCategoriesPath($content, $categoryPath, $categoryUrl);
            }
        }
        return $content;
    }

    /**
     * @param $content
     * @return array
     */
    protected function getCategoriesMatches($content)
    {
        $regexp = '/{.(?:category.+)(?:url=(?:"([^"]*)")).?(?:attribute=(?:"([^"]*)"))?(?:.}+)/';
        preg_match_all($regexp, $content, $matches);
        return array('path' => $matches[1], 'attribute' => $matches[2]);
    }

    /**
     * @param $content
     * @param $urlPath
     * @param $categoryUrl
     * @return mixed
     */
    protected function replaceContentCategoriesPath($content, $urlPath, $categoryUrl)
    {
        $urlPath = str_replace('/', '\/', $urlPath);
        if (strpos($urlPath, '?')) {
            $urlPath = array_filter(explode("?", $urlPath));
            $regexp = '/{.(category).*(url="(' . $urlPath[0] . ')").*(attribute="('. $urlPath[1] .')").*(.})/';
        } else {
            $regexp = '/{.(category).*(url="(' . $urlPath .')").*(.})/';
        }
        return preg_replace($regexp, $categoryUrl, $content);
    }

    /**
     * @param $categoriesReplacement
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCategoriesUrl($categoriesReplacement)
    {
        $categoryData = array();
        foreach ($categoriesReplacement['path'] as $categoryNumber => $path) {
            $category = $this->categoryLoader->load($this->getCategoryId($path));
            if (!empty($category)) {
                $categoryUrl =  $category->getUrl();
                if (!empty($categoriesReplacement['attribute'][$categoryNumber])) {
                    $urlAttributes = $categoriesReplacement['attribute'][$categoryNumber];
                    $categoryUrl .= '?' . $this->getUrlFilter($urlAttributes);
                    $path = $path . '?' . $urlAttributes;
                }
                $categoryData[$path] = $categoryUrl;
                unset($categoryUrl);
            }
        }
        return $categoryData;
    }

    /**
     * @param $urlAttributes
     * @return string
     */
    protected function getUrlFilter($urlAttributes)
    {
        $separatedAttributes = $this->getArrayValue($urlAttributes, ';');
        foreach ($separatedAttributes as $attributeNumber => $attributeValue) {
            $attributeData = $this->getArrayValue($attributeValue, '=');
            $attributeOptions = $this->productConverter->getAttributeOptions($attributeData[0]);
            $attributeValue = $attributeOptions->getItemByColumnValue('value', $attributeData[1]);
            if ($attributeNumber == 0) {
                $urlFilter = $attributeData[0] . '=' . $attributeValue->getId();
                continue;
            }
            $urlFilter .= '&' . $attributeData[0] . '=' . $attributeValue->getId();
        }
        return $urlFilter;
    }

}
