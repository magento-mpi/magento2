<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Resource\Oauth\Nonce;

/**
 * OAuth nonce resource collection model
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Integration\Model\Oauth\Nonce', 'Magento\Integration\Model\Resource\Oauth\Nonce');
    }
}
