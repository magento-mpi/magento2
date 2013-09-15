<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab;

class Downloadable
    extends \Magento\Adminhtml\Block\Widget implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Reference to product objects that is being edited
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product = null;

    protected $_config = null;

    protected $_template = 'product/edit/downloadable.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check is readonly block
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getDownloadableReadonly();
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Downloadable Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Downloadable Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs::ADVANCED_TAB_GROUP_CODE;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Accordion')
            ->setId('downloadableInfo');

        $accordion->addItem('samples', array(
            'title'   => __('Samples'),
            'content' => $this->getLayout()
                ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples')
                ->toHtml(),
            'open'    => false,
        ));

        $accordion->addItem('links', array(
            'title'   => __('Links'),
            'content' => $this->getLayout()->createBlock(
                'Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links',
                'catalog.product.edit.tab.downloadable.links')->toHtml(),
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }
}
