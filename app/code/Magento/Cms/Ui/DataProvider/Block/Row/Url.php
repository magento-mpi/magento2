<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\DataProvider\Block\Row;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\RowInterface;

/**
 * Class Url
 */
class Url implements RowInterface
{
    /**
     * Url path
     */
    const URL_PATH = 'cms/block/edit';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get data
     *
     * @param array $dataRow
     * @return mixed
     */
    public function getData(array $dataRow)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH, ['block_id' => $dataRow['block_id']]);
    }
}
