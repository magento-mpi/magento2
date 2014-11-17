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
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader
     * @param \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter $productConverter
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
            if ('content' == $field) {
                $data['block'][$field] = $this->replaceMatches($value);
                continue;
            }
            $data['block'][$field] = $value;
        }
         return $data;
    }

    /**
     * @param string $urlKey
     * @return \Magento\Framework\Object
     */
    protected function getCategoryByUrlKey($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addUrlRewriteToResult()
            ->getFirstItem();
        return $category;
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
     * @param string $content
     * @return mixed
     */
    protected function replaceMatches($content)
    {
        $matches = $this->getMatches($content);
        if (!empty($matches['path'])) {
            $replaces = $this->getReplaces($matches);
            preg_replace($replaces['regexp'], $replaces['value'], $content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @return array
     */
    protected function getMatches($content)
    {
        $regexp = '/{{((?:category[^"]+))(?:url=(?:"([^"]*)")).?(?:attribute=(?:"([^"]*)"))?(?:}}+)/';
        preg_match_all($regexp, $content, $matches);
        return array('path' => $matches[2], 'attribute' => $matches[3], 'type' => $matches[1]);
    }

    /**
     * @param array $matches
     * @return array
     */
    protected function getReplaces($matches)
    {
        $replaceData = array();

        foreach ($matches['path'] as $matchKey => $matchValue) {
            $category = $this->getCategoryByUrlKey($matchValue);
            if (empty($category)) {
                continue;
            }
            $type = trim($matches['type'][$matchKey]);
            switch ($type) {
                case 'category':
                    $categoryUrl = $category->getRequestPath();
                    if (!empty($matches['attribute'][$matchKey])) {
                        $urlAttributes = $matches['attribute'][$matchKey];
                        $categoryUrl .= '?' . $this->getUrlFilter($urlAttributes);
                        $matchValue = $matchValue . '?' . $urlAttributes;

                        $key = array_filter(explode("?", $matchValue));
                        $replaceData['regexp'][] =
                            '/{.(category).*(url="(' . $key[0] . ')").*(attribute="('. $key[1] .')").*(.})/';
                    } else {
                        $replaceData['regexp'][] = '/{.(category).*(url="(' . $matchValue .')").*(.})/';
                    }
                    $replaceData['value'][] = '{{store url=""}}' . $categoryUrl;
                    break;
                case 'categoryId':
                    $replaceData['regexp'][] = '/{.(categoryId).*(url="(' . $matchValue .')").*(.})/';
                    $replaceData['value'][] = sprintf('%03d', $category->getId());
                    break;
            }
        }
        return $replaceData;
    }

    /**
     * @param string $urlAttributes
     * @return string
     */
    protected function getUrlFilter($urlAttributes)
    {
        $separatedAttributes = $this->getArrayValue($urlAttributes, ';');
        $urlFilter = null;
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
