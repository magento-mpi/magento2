<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\DataProvider\Page\Row;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\RowInterface;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

/**
 * Class Actions
 */
class Actions implements RowInterface
{
    /**
     * Url path
     */
    const URL_PATH = 'cms/page/edit';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlBuilder $actionUrlBuilder, UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
    }

    /**
     * Get data
     *
     * @param array $dataRow
     * @return mixed
     */
    public function getData(array $dataRow)
    {
        return [
            'edit' => [
                'href' => $this->urlBuilder->getUrl(static::URL_PATH, ['page_id' => $dataRow['page_id']]),
                'title' => __('Edit'),
                'hidden' => true

            ],
            'preview' => [
                'href' => $this->actionUrlBuilder->getUrl(
                    $dataRow['identifier'],
                    isset($dataRow['_first_store_id']) ? $dataRow['_first_store_id'] : null,
                    isset($dataRow['store_code']) ? $dataRow['store_code'] : null
                ),
                'title' => __('Preview')
            ]
        ];
    }
}
