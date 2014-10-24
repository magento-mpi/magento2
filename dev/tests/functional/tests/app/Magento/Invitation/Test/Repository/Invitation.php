<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Invitation.
 * Repository for invitation.
 */
class Invitation extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'email' => 'test%isolation%@example.com',
            'message' => 'Wish list %isolation%',
            'store_id' => ['dataSet' => 'default'],
            'group_id' => ['dataSet' => 'General'],
        ];

        $this->_data['invitation_with_two_emails'] = [
            'email' => 'test%isolation%_1@example.com, test%isolation%_2@example.com',
            'message' => 'Wish list %isolation%',
            'store_id' => ['dataSet' => 'default'],
            'group_id' => ['dataSet' => 'General'],
        ];
    }
}
