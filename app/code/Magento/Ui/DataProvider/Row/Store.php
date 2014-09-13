<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider\Row;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\RowInterface;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Store
 */
class Store implements RowInterface
{
    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Constructor
     * @param SystemStore $systemStore
     */
    public function __construct(SystemStore $systemStore)
    {
        $this->systemStore = $systemStore;
    }

    /**
     * Get data
     *
     * @param array $dataRow
     * @return mixed
     */
    public function getData(array $dataRow)
    {
        $content = '';
        $origStores = $dataRow['store_id'];

        if (empty($origStores)) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }
        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }

        $data = $this->systemStore->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $content .= $website['label'] . "<br/>";
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $group['label'] . "<br/>";
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $store['label'] . "<br/>";
                }
            }
        }

        return $content;
    }
}
