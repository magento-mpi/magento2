<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward resource setup model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * Current entity type id
     *
     * @var string
     */
    protected $_currentEntityTypeId;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $resourceName,
        $moduleName = 'Magento_Reward',
        $connectionName = ''
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $config, $cache, $migrationFactory, $coreData,
            $resourceName, $moduleName, $connectionName
        );
    }

    /**
     * Add attribute to an entity type
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return \Magento\Eav\Model\Entity\Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $this->_currentEntityTypeId = $entityTypeId;
        return parent::addAttribute($entityTypeId, $code, $attr);
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }

    /**
     * Prepare attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        if ($this->_currentEntityTypeId == 'customer') {
            $data = array_merge($data, array(
                'is_visible'                => $this->_getValue($attr, 'visible', 1),
                'is_visible_on_front'       => $this->_getValue($attr, 'visible_on_front', 0),
                'input_filter'              => $this->_getValue($attr, 'input_filter', ''),
                'lines_to_divide_multiline' => $this->_getValue($attr, 'lines_to_divide', 0),
                'min_text_length'           => $this->_getValue($attr, 'min_text_length', 0),
                'max_text_length'           => $this->_getValue($attr, 'max_text_length', 0)
            ));
        }
        return $data;
    }
}
