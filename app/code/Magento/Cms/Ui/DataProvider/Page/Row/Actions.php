<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\DataProvider\Page\Row;

use Magento\Ui\DataProvider\RowInterface;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

/**
 * Class Actions
 */
class Actions implements RowInterface
{
    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @param UrlBuilder $actionUrlBuilder
     */
    public function __construct(UrlBuilder $actionUrlBuilder)
    {
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
            'href' => $this->actionUrlBuilder->getUrl(
                $dataRow['identifier'],
                isset($dataRow['_first_store_id']) ? $dataRow['_first_store_id'] : null,
                isset($dataRow['store_code']) ? $dataRow['store_code'] : null
            ),
            'title' => __('Preview')
        ];
    }
}
