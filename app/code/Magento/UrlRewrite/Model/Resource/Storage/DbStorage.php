<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\Resource\Storage;

use Magento\Framework\Model\Resource\Db\AbstractDb;
use Magento\UrlRewrite\Model\Storage\DbStorage as DbStorageModel;

class DbStorage extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(DbStorageModel::TABLE_NAME, 'url_rewrite_id');
    }
}
