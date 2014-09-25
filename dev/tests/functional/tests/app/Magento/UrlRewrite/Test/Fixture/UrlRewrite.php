<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class UrlRewrite
 */
class UrlRewrite extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\UrlRewrite\Test\Repository\UrlRewrite';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\UrlRewrite\Test\Handler\UrlRewrite\UrlRewriteInterface';

    protected $defaultDataSet = [
        'store_id' => 'Main Website/Main Website Store/Default Store View',
        'request_path' => 'test_request%isolation%',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $id_path = [
        'attribute_code' => 'id_path',
        'backend_type' => 'virtual',
        'source' => 'Magento\UrlRewrite\Test\Fixture\UrlRewrite\IdPath',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => 'Default Store View',
        'source' => 'Magento\UrlRewrite\Test\Fixture\UrlRewrite\StoreId',
        'input' => 'select',
    ];

    protected $redirect_type = [
        'attribute_code' => 'redirect_type',
        'backend_type' => 'int',
        'is_required' => '0',
        'input' => 'select',
    ];

    protected $request_path = [
        'attribute_code' => 'request_path',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => 'request_path%isolation%',
        'input' => 'text',
    ];

    protected $target_path = [
        'attribute_code' => 'target_path',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => 'target_path%isolation%',
        'input' => 'text',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'input' => 'text',
    ];

    public function getId()
    {
        return $this->getData('id');
    }

    public function getIdPath()
    {
        return $this->getData('id_path');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getRedirectType()
    {
        return $this->getData('redirect_type');
    }

    public function getRequestPath()
    {
        return $this->getData('request_path');
    }

    public function getTargetPath()
    {
        return $this->getData('target_path');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }
}
