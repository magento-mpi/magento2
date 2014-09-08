<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Ui\Provider\Action;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

/**
 * Class Row
 */
class Row implements \Magento\Ui\Provider\ProviderInterface
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
     * @param array $dataRow
     * @return array
     */
    public function provide(array $dataRow)
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
