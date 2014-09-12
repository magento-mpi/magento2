<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\DataProvider\Row;

use Magento\Ui\DataProvider\RowInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Url
 */
class Url implements RowInterface
{
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
        return $this->urlBuilder->getUrl('*/*/edit', ['page_id' => $dataRow['page_id']]);
    }
}
