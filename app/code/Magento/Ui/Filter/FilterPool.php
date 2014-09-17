<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter;

use Magento\Framework\ObjectManager;

/**
 * Class FilterPool
 */
class FilterPool
{
    /**
     * Filter types
     *
     * @var array
     */
    protected $filterTypes = [
        'filter_input' => 'Magento\Ui\Filter\Type\Input',
        'filter_select' => 'Magento\Ui\Filter\Type\Select',
        'filter_range' => 'Magento\Ui\Filter\Type\Range',
        'filter_date' => 'Magento\Ui\Filter\Type\Date',
        'filter_store' => 'Magento\Ui\Filter\Type\Store'
    ];

    /**
     * Filters poll
     *
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get filter by type
     *
     * @param string $dataType
     * @return FilterInterface
     * @throws \InvalidArgumentException
     */
    public function getFilter($dataType)
    {
        if (!isset($this->filters[$dataType])) {
            if (!isset($this->filterTypes[$dataType])) {
                throw new \InvalidArgumentException(sprintf('Unknown filter type "%s"', $dataType));
            }
            $this->filters[$dataType] = $this->objectManager->create($this->filterTypes[$dataType]);
        }

        return $this->filters[$dataType];
    }
}
