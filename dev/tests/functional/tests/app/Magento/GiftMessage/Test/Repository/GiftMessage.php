<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GiftMessage
 * GiftMessage repository
 */
class GiftMessage extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'sender' => 'John Doe',
            'recipient' => 'Jane Doe',
            'message' => 'text_%isolation%',
        ];
    }
}
