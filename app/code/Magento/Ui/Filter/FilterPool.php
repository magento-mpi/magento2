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
        'input' => 'Magento\Ui\Filter\Type\Input',
        'select' => 'Magento\Ui\Filter\Type\Select',
        'date' => 'Magento\Ui\Filter\Type\Date'
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
     * @param $dataType
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
