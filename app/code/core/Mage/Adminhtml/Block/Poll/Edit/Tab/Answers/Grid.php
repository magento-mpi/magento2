<?php
/**
 * Poll answers grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Edit_Tab_Answers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('answersGrid');
        $this->setDefaultSort('answer_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('poll/poll_answer')
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
            'header'    => __('Votes Count'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'votes_count',
        ));

        $this->addColumn('actions', array(
            'header'    => __('Actions'),
            'align'     => 'center',
            'type'      => 'action',
            'width'     => '10px',
            'filter'    => false,
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
        return Mage::getUrl('*/poll_answer/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/poll_answer/grid', array('id' => $this->getRequest()->getParam('id')));
    }
}