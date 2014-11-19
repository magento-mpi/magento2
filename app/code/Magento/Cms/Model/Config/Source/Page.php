<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Config\Source;

/**
 * Class Page
 */
class Page implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var \Magento\Cms\Api\PageCriteriaInterfaceFactory
     */
    protected $pageCriteriaFactory;

    /**
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param \Magento\Cms\Api\PageCriteriaInterfaceFactory $pageCriteriaFactory
     */
    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Cms\Api\PageCriteriaInterfaceFactory $pageCriteriaFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageCriteriaFactory = $pageCriteriaFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->pageRepository->getList($this->pageCriteriaFactory->create())->toOptionIdArray();
        }
        return $this->options;
    }
}
