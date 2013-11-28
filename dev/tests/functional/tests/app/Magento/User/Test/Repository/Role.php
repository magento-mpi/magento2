<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Repository\AbstractRepository;
use Zend\Code\Exception\InvalidArgumentException;

/**
 * Class Abstract Repository
 *
 * @package namespace Magento\User\Test\Repository
 */
class Role extends AbstractRepository
{
    /**
     * @var array
     */
    protected $_config = array();


    /**
     * Add role permission values to data array
     *
     * @param $permissions string. Possible values 'custom' or 'all'
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     */
    protected function setPermissions($permissions, $data = array())
    {
        if ('all' == $permissions) {
            $data['fields']['all']['value'] = 1;
        } elseif('custom' == $permissions) {
            $data['fields']['all']['value'] = 0;
            $data['fields']['resource']['value'] = array();
        } else {
            throw new InvalidArgumentException('Invalid permissions "' . $permissions . '"');
        }
        return $data;
    }

    /**
     * @param $scope string. possible values  'all', 'website', 'store'
     * @param array $data
     * @return array
     * @throws \Zend\Code\Exception\InvalidArgumentException
     */
    protected function setScope($scope, $data = array())
    {
        switch($scope) {
            case 'all':
                $data['fields']['gws_is_all']['value'] = 1;
                break;
            case 'website':
                $data['fields']['gws_is_all']['value'] = 0;
                $data['fields']['gws_websites']['value'] = array();
                break;
            case 'store':
                $data['fields']['gws_is_all']['value'] = 0;
                $data['fields']['gws_store_groups']['value'] = array();
                break;
            default:
                throw new InvalidArgumentException('Invalid role scope "' . $scope . '"');
        }
        return $data;
    }


    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $dataTemplate = array(
            'fields' => array(
                'all' => array(
                    'value' => 0,
                ),
                'gws_is_all' => array(
                    'value' => 0,
                ),
                'rolename' => array(
                    'value' => 'auto%isolation%',
                ),
            )
        );

        $this->_data['all_permissions_all_scopes']['data'] = $this->setPermissions(
            'all',
            $this->setScope('all', $dataTemplate)
        );

        $this->_data['all_permissions_website_scope']['data'] = $this->setPermissions(
            'all',
            $this->setScope('website', $dataTemplate)
        );

        $this->_data['all_permissions_store_scope']['data'] = $this->setPermissions(
            'all',
            $this->setScope('store', $dataTemplate)
        );

        $this->_data['custom_permissions_all_scopes']['data'] = $this->setPermissions(
            'custom',
            $this->setScope('all', $dataTemplate)
        );

        $this->_data['custom_permissions_website_scope']['data'] = $this->setPermissions(
            'custom',
            $this->setScope('website', $dataTemplate)
        );

        $this->_data['custom_permissions_store_scope']['data'] = $this->setPermissions(
            'custom',
            $this->setScope('store', $dataTemplate)
        );
    }

    /**
     * Save custom data set to repository
     *
     * @param $dataSetName
     * @param array $data
     */
    public function set($dataSetName, array $data)
    {
        $this->_data[$dataSetName]['data'] = $data;
    }
}