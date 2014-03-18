<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model\Resource\Indexer;

class State extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('indexer_state', 'state_id');
        $this->addUniqueField(array(
            'field' => array('indexer_id'),
            'title' => __('State for the same indexer')
        ));
    }
}
