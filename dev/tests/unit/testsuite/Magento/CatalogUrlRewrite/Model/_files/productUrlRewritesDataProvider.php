<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

return [
    [1, null, 'simple-product.html', null, null, [
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'simple-product.html',
            'target_path' => 'catalog/product/view/id/1',
            'redirect_type' => 0,
            'is_autogenerated' => true,
            'metadata' => null
        ]
    ]],
    [1, 2, 'simple-product.html', 'category/simple-product.html', [
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-autogenerated.html',
            UrlRewrite::TARGET_PATH => 'some-path.html',
            UrlRewrite::STORE_ID => 2,
            UrlRewrite::IS_AUTOGENERATED => 1,
            UrlRewrite::METADATA => null,
        ],
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-autogenerated-with-category-and-metadata.html',
            UrlRewrite::TARGET_PATH => 'some-path.html',
            UrlRewrite::STORE_ID => 2,
            UrlRewrite::IS_AUTOGENERATED => 1,
            UrlRewrite::METADATA => ['category_id' => 2, 'some_another_data' => 1],
        ],
        [
            UrlRewrite::REQUEST_PATH => 'category/simple-product.html',
            UrlRewrite::TARGET_PATH => 'skip-generation-due-to-equals-request-and-generated-target-path.html',
            UrlRewrite::IS_AUTOGENERATED => 1,
            UrlRewrite::METADATA => ['category_id' => 2],
        ],
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-custom-by-user.html',
            UrlRewrite::TARGET_PATH => 'custom-target-path.html',
            UrlRewrite::REDIRECT_TYPE => 'some-type',
            UrlRewrite::IS_AUTOGENERATED => 0,
            UrlRewrite::METADATA => ['is_user_generated' => 1],
        ],
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-custom-without-redirect-type.html',
            UrlRewrite::TARGET_PATH => 'custom-target-path2.html',
            UrlRewrite::REDIRECT_TYPE => 0,
            UrlRewrite::IS_AUTOGENERATED => 0,
            UrlRewrite::METADATA => ['is_user_generated' => false],
        ],
        [
            UrlRewrite::REQUEST_PATH => 'skip-equals-paths.html',
            UrlRewrite::TARGET_PATH => 'skip-equals-paths.html',
            UrlRewrite::REDIRECT_TYPE => 0,
            UrlRewrite::IS_AUTOGENERATED => 0,
            UrlRewrite::METADATA => ['is_user_generated' => 1],
        ],
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-custom-without-category.html',
            UrlRewrite::TARGET_PATH => 'same-path',
            UrlRewrite::REDIRECT_TYPE => 302,
            UrlRewrite::IS_AUTOGENERATED => 0,
            UrlRewrite::METADATA => null,
        ],
        [
            UrlRewrite::REQUEST_PATH => 'generate-for-custom-with-category.html',
            UrlRewrite::TARGET_PATH => 'same-path',
            UrlRewrite::REDIRECT_TYPE => 302,
            UrlRewrite::IS_AUTOGENERATED => 0,
            UrlRewrite::METADATA => ['category_id' => 2],
        ],
    ], [
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'simple-product.html',
            'target_path' => 'catalog/product/view/id/1',
            'redirect_type' => 0,
            'is_autogenerated' => true,
            'metadata' => null
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'category/simple-product.html',
            'target_path' => 'catalog/product/view/id/1/category/2',
            'redirect_type' => 0,
            'is_autogenerated' => true,
            'metadata' => serialize(['category_id' => 2])
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 2,
            'request_path' => 'generate-for-autogenerated.html',
            'target_path' => 'simple-product.html',
            'redirect_type' => OptionProvider::PERMANENT,
            'is_autogenerated' => false,
            'metadata' => null
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 2,
            'request_path' => 'generate-for-autogenerated-with-category-and-metadata.html',
            'target_path' => 'category/simple-product.html',
            'redirect_type' => OptionProvider::PERMANENT,
            'is_autogenerated' => false,
            'metadata' => serialize(['category_id' => 2, 'some_another_data' => 1]),
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'generate-for-custom-by-user.html',
            'target_path' => 'custom-target-path.html',
            'redirect_type' => 'some-type',
            'is_autogenerated' => false,
            'metadata' => serialize(['is_user_generated' => 1]),
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'generate-for-custom-without-redirect-type.html',
            'target_path' => 'custom-target-path2.html',
            'redirect_type' => 0,
            'is_autogenerated' => false,
            'metadata' => serialize(['is_user_generated' => false]),
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'generate-for-custom-without-category.html',
            'target_path' => 'simple-product.html',
            'redirect_type' => 302,
            'is_autogenerated' => false,
            'metadata' => null,
        ],
        [
            'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE_PRODUCT,
            'entity_id' => 1,
            'store_id' => 1,
            'request_path' => 'generate-for-custom-with-category.html',
            'target_path' => 'category/simple-product.html',
            'redirect_type' => 302,
            'is_autogenerated' => false,
            'metadata' => serialize(['category_id' => 2]),
        ],
    ]],
];
