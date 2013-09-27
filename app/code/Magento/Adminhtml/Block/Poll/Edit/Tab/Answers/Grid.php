<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll answers grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Poll_Edit_Tab_Answers_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Magento_Poll_Model_Poll_AnswerFactory
     */
    protected $_pollAnswerFactory;

    /**
     * @param Magento_Poll_Model_Poll_AnswerFactory $pollAnswerFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Poll_Model_Poll_AnswerFactory $pollAnswerFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_urlModel = $urlModel;
        $this->_pollAnswerFactory = $pollAnswerFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('answersGrid');
        $this->setDefaultSort('answer_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_pollAnswerFactory->create()
            ->getResourceCollection()
            ->addPollFilter($this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('answer_id', array(
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'answer_id',
        ));

        $this->addColumn('answer_title', array(
            'header'    => __('Answer Title'),
            'align'     =>'left',
            'index'     => 'answer_title',
        ));

        $this->addColumn('votes_count', array(
            'header'    => __('Votes'),
            'type'      => 'number',
            'width'     => '50px',
            'index'     => 'votes_count',
        ));

        $this->addColumn('actions', array(
            'header'    => __('Actions'),
            'align'     => 'center',
            'type'      => 'action',
            'width'     => '10px',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'   => __('Delete'),
                    'onClick'   => 'return answers.delete(\'$answer_id\')',
                    'url'       => '#',
                ),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/poll_answer/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/poll_answer/grid', array('id' => $this->getRequest()->getParam('id')));
    }

}
