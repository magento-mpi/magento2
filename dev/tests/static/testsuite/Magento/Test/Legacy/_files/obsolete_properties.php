<?php
/**
 * Obsolete class attributes
 *
 * Format: array(<attribute_name>[, <class_scope> = ''[, <replacement>]])
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('_addresses', 'Magento\Customer\Model\Customer'),
    array('_addMinimalPrice', 'Magento\Catalog\Model\Resource\Product\Collection'),
    array('_alias', 'Magento\Core\Block\AbstractBlock'),
    array('_anonSuffix'),
    array('_appMode', 'Magento\Framework\App\ObjectManager\ConfigLoader'),
    array('_baseDirCache', 'Magento\Core\Model\Config'),
    array('_cacheConf'),
    array('_cachedItemPriceBlocks'),
    array('_canUseLocalModules'),
    array('_checkedProductsQty', 'Magento\CatalogInventory\Model\Observer'),
    array('_children', 'Magento\Core\Block\AbstractBlock'),
    array('_childrenHtmlCache', 'Magento\Core\Block\AbstractBlock'),
    array('_childGroups', 'Magento\Core\Block\AbstractBlock'),
    array('_combineHistory'),
    array('_config', 'Magento\Core\Model\Design\Package'),
    array('_config', 'Magento\Framework\Logger', '_dirs'),
    array('_config', 'Magento\Core\Model\Resource\Setup'),
    array('_configModel', 'Magento\Backend\Model\Menu\AbstractDirector'),
    array('_connectionConfig', 'Magento\Core\Model\Resource\Setup'),
    array('_connectionTypes', 'Magento\Framework\App\Resource'),
    array('_currencyNameTable'),
    array('_customEtcDir', 'Magento\Core\Model\Config'),
    array('_customerFactory ', '\Magento\Sales\Block\Adminhtml\Billing\Agreement\View\Tab\Info'),
    array('_customerFactory ', '\Magento\Sales\Block\Adminhtml\Order\Create\Form\Account'),
    array('_customerFormFactory', '\Magento\Sales\Block\Adminhtml\Order\Create\Form\Account'),
    array('_defaultTemplates', 'Magento\Email\Model\Template'),
    array('_designProductSettingsApplied'),
    array('_directOutput', 'Magento\Framework\View\Layout'),
    array('_dirs', 'Magento\Framework\App\Resource'),
    array('_entityInvalidatedIndexes', 'Magento\ImportExport\Model\Import'),
    array('_distroServerVars'),
    array('_entityIdsToIncrementIds'),
    array('entities', 'Magento\Framework\App\Resource'),
    array('_entityTypeIdsToTypes'),
    array('_factory', 'Magento\Backend\Model\Menu\Config'),
    array('_factory', 'Magento\Backend\Model\Menu\AbstractDirector', '_commandFactory'),
    array('_isAnonymous'),
    array('_isFirstTimeProcessRun', 'Magento\SalesRule\Model\Validator'),
    array('_isGoogleCheckoutLinkAdded', 'Magento\GoogleAnalytics\Model\Observer'),
    array('_isRuntimeValidated', 'Magento\Framework\ObjectManager\Config\Reader\Dom'),
    array('_itemPriceBlockTypes'),
    array('_loadDefault', 'Magento\Store\Model\Resource\Store\Collection'),
    array('_loadDefault', 'Magento\Store\Model\Resource\Group\Collection'),
    array('_loadDefault', 'Magento\Store\Model\Resource\Website\Collection'),
    array('_mapper', 'Magento\Framework\ObjectManager\Config\Reader\Dom'),
    array('_menu', 'Magento\Backend\Model\Menu\Builder'),
    array('_modulesReader', 'Magento\Framework\App\ObjectManager\ConfigLoader'),
    array('_moduleReader', 'Magento\Backend\Model\Menu\Config'),
    array('_option', 'Magento\Captcha\Helper\Data', '_dirs'),
    array('_options', 'Magento\Core\Model\Config', 'Magento\Framework\Filesystem'),
    array('_optionsMapping', null, '\Magento\Framework\Filesystem::getDirectoryRead($nodeKey)->getAbsolutePath()'),
    array('_order', 'Magento\Checkout\Block\Onepage\Success'),
    array('_order_id'),
    array('_parent', 'Magento\Core\Block\AbstractBlock'),
    array('_parentBlock', 'Magento\Core\Block\AbstractBlock'),
    array('_persistentCustomerGroupId'),
    array('_queriesHooked', 'Magento\Core\Model\Resource\Setup'),
    array('_quoteImporter', 'Magento\Paypal\Model\Express\Checkout'),
    array('_ratingOptionTable', 'Magento\Review\Model\Resource\Rating\Option\Collection'),
    array('_readerFactory', 'Magento\Framework\App\ObjectManager\ConfigLoader'),
    array('_resourceConfig', 'Magento\Core\Model\Resource\Setup'),
    array('_saveTemplateFlag', 'Magento\Newsletter\Model\Queue'),
    array('_searchTextFields'),
    array('_setAttributes', 'Magento\Catalog\Model\Product\Type\AbstractType'),
    array('_skipFieldsByModel'),
    array('_ship_id'),
    array('_shipTable', 'Magento\OfflineShipping\Model\Resource\Carrier\Tablerate\Collection'),
    array(
        '_showTemplateHints',
        'Magento\Framework\View\Element\Template',
        'Magento\Core\Model\TemplateEngine\Plugin\DebugHints'
    ),
    array(
        '_showTemplateHintsBlocks',
        'Magento\Framework\View\Element\Template',
        'Magento\Core\Model\TemplateEngine\Plugin\DebugHints'
    ),
    array('_sortedChildren'),
    array('_sortInstructions'),
    array('_storeFilter', 'Magento\Catalog\Model\Product\Type\AbstractType'),
    array('_substServerVars'),
    array('_track_id'),
    array('_varSubFolders', null, 'Magento\Framework\Filesystem'),
    array('_viewDir', 'Magento\Framework\View\Element\Template', '_dirs'),
    array('decoratedIsFirst', null, 'getDecoratedIsFirst'),
    array('decoratedIsEven', null, 'getDecoratedIsEven'),
    array('decoratedIsOdd', null, 'getDecoratedIsOdd'),
    array('decoratedIsLast', null, 'getDecoratedIsLast'),
    array('static', 'Magento\Email\Model\Template\Filter'),
    array('_addressForm', 'Magento\Sales\Block\Adminhtml\Order\Create\Form\Address'),
    array('_adminhtmlAddresses', 'Magento\Sales\Block\Adminhtml\Order\Create\Form\Address'),
    array('_useAnalyticFunction'),
    array('_defaultIndexer', 'Magento\CatalogInventory\Model\Resource\Indexer\Stock'),
    array('_engine', 'Magento\CatalogSearch\Model\Resource\Fulltext'),
    array('_allowedAreas', 'Magento\Core\Model\Config'),
    array('_app', 'Magento\Core\Block\AbstractBlock'),
    array('_app', 'Magento\Framework\View\Element\Template'),
    array('_config', 'Magento\Backend\Helper\Data'),
    array('_defaultAreaFrontName', 'Magento\Backend\Helper\Data'),
    array('_areaFrontName', 'Magento\Backend\Helper\Data'),
    array('_backendFrontName', 'Magento\Backend\Helper\Data'),
    array('_app', 'Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency'),
    array('_enginePool', '\Magento\Framework\View\Element\Template\Context', '_engineFactory'),
    array('_fileHandler', '\Magento\Sitemap\Model\Sitemap', '_stream'),
    array('_fileIo', '\Magento\Theme\Model\Uploader\Service', '_filesystem'),
    array('_streamFactory', '\Magento\Core\Model\File\Storage\Config', '_filesystem'),
    array('_streamFactory', '\Magento\Core\Model\File\Storage\Synchronization', '_filesystem'),
    array('_allowedFormats', '\Magento\Core\Helper\Data', '\Magento\Framework\Locale'),
    array('types', '\Magento\Core\Model\Theme'),
    array('_collectionFactory', '\Magento\Install\Controller\Action', 'themeProvider'),
    array('_collectionFactory', '\Magento\Theme\Model\Config\Customization', 'themeProvider'),
    array('_message', 'Magento\Checkout\Model\Cart', 'messageFactory'),
    array('_message', 'Magento\Core\Model\Session\AbstractSession', 'messageFactory'),
    array('_messageFactory', 'Magento\Core\Model\Session\AbstractSession', 'messagesFactory'),
    array('_message', 'Magento\Core\Model\Session\Context', 'messageFactory'),
    array('_messageFactory', 'Magento\Core\Model\Session\Context', 'messagesFactory'),
    array('_sessionQuote', 'Magento\Sales\Block\Adminhtml\Order\Create\Messages', 'sessionQuote'),
    array('_coreRegistry', 'Magento\Sales\Block\Adminhtml\Order\View\Messages', 'coreRegistry'),
    array('_message', 'Magento\Sales\Model\Quote', 'messageFactory'),
    array('_encryptedSessionId', '\Magento\Core\Model\Session\AbstractSession'),
    array('_skipSessionIdFlag', '\Magento\Core\Model\Session\AbstractSession'),
    array('_url', '\Magento\Core\Model\Session\AbstractSession'),
    array('_sidNameMap', '\Magento\Core\Model\Session\AbstractSession'),
    array('_store', '\Magento\Framework\Stdlib\Cookie'),
    array('_lifetime', '\Magento\Framework\Stdlib\Cookie'),
    array('_httpResponse', '\Magento\Framework\Stdlib\Cookie'),
    array('_storeManager', '\Magento\Framework\Stdlib\Cookie'),
    array('_scopeConfig', '\Magento\Framework\Stdlib\Cookie'),
    array('_savePath', '\Magento\Core\Model\Session\Context'),
    array('_cacheLimiter', '\Magento\Core\Model\Session\Context'),
    array('_dir', '\Magento\Core\Model\Session\Context'),
    array('_saveMethod', '\Magento\Core\Model\Session\Context'),
    array('_appState', '\Magento\Core\Model\Session\Context'),
    array('_validator', '\Magento\Core\Model\Session\Context'),
    array('_logger', '\Magento\Core\Model\Session\Context'),
    array('_eventManager', '\Magento\Core\Model\Session\Context'),
    array('_scopeConfig', '\Magento\Core\Model\Session\Context'),
    array('messageFactory', '\Magento\Core\Model\Session\Context'),
    array('messagesFactory', '\Magento\Core\Model\Session\Context'),
    array('_request', '\Magento\Core\Model\Session\Context'),
    array('_storeManager', '\Magento\Core\Model\Session\Context'),
    array('_cacheLimiter', 'Magento\Core\Model\Session\AbstractSession'),
    array('_saveMethod', 'Magento\Core\Model\Session\AbstractSession'),
    array('_appState', 'Magento\Core\Model\Session\AbstractSession'),
    array('_dir', 'Magento\Core\Model\Session\AbstractSession'),
    array('_savePath', 'Magento\Core\Model\Session\AbstractSession'),
    array('_filesystem', '\Magento\Cms\Helper\Wysiwyg\Images', '_directory'),
    array('_filesystem', '\Magento\Cms\Model\Wysiwyg\Images\Storage', '_directory'),
    array('_filesystem', '\Magento\Core\Model\Page\Asset\MergeStrategy\Direct', '_directory'),
    array('_filesystem', '\Magento\Core\Model\Page\Asset\MergeStrategy\Checksum', '_directory'),
    array('_filesystem', 'Magento\Sales\Model\Order\Pdf\AbstractPdf'),
    array('_baseDir', 'Magento\Core\Model\Resource\Setup\Migration'),
    array('_dir', 'Magento\Core\Model\Resource\Setup\Migration'),
    array('_filesystem', 'Magento\Core\Model\Resource\Setup\Migration', '_directory'),
    array('_filesystem', 'Magento\Core\Model\Theme\Collection', '_directory'),
    array('_mediaBaseDirectory', 'Magento\Core\Model\Resource\File\Storage\File'),
    array('_dbHelper', 'Magento\Core\Model\Resource\File\Storage\File'),
    array('_filesystem', 'Magento\Core\Model\Theme\CopyService', '_directory'),
    array('_baseDir', 'Magento\Core\Model\Theme\Collection'),
    array('_filesystem', 'Magento\Downloadable\Controller\Adminhtml\Downloadable\File'),
    array('_dirModel', 'Magento\Downloadable\Controller\Adminhtml\Downloadable\File'),
    array('_dirModel', 'Magento\Downloadable\Model\Link'),
    array('_dirModel', 'Magento\Downloadable\Model\Sample'),
    array('_dir', 'Magento\Framework\App\Dir'),
    array('_baseDir', 'Magento\Backup\Model\Fs\Collection'),
    array('_filesystem', 'Magento\Backup\Model\Fs\Collection'),
    array('_dir', 'Magento\Backup\Model\Fs\Collection'),
    array('_dir', 'Magento\Cms\Model\Wysiwyg\Images\Storage'),
    array('_dirs', 'Magento\Core\Helper\Theme'),
    array('_dirs', 'Magento\Framework\Model\Resource\Type\Db\Pdo\Mysql'),
    array('_filesystem', 'Magento\GiftWrapping\Model\Wrapping'),
    array('_customer', 'Magento\Backend\Model\Session\Quote'),
    array('_customerFactory', 'Magento\Backend\Model\Session\Quote'),
    array('_coreDir', 'Magento\Sales\Model\Order\Pdf\AbstractPdf'),
    array('_coreDir', 'Magento\ScheduledImportExport\Model\Scheduled\Operation'),
    array('_dirs', 'Magento\Core\Block\Template'),
    array('_applicationDirs', 'Magento\Framework\App\Config\FileResolver'),
    array('_dir', 'Magento\Core\Model\File\Storage'),
    array('_dirs', 'Magento\Core\Block\Template\Context'),
    array('_dir', 'Magento\Core\Model\Page\Asset\MergeService'),
    array('_dir', 'Magento\Core\Model\Page\Asset\MinifyService'),
    array('_dir', 'Magento\Core\Model\Resource'),
    array('_dir', 'Magento\Core\Model\Session\Context'),
    array('dir', 'Magento\Core\Model\Theme\Image\Path'),
    array('_dir', 'Magento\Install\App\Action\Plugin\Dir'),
    array('_dirs', 'Magento\Framework\View\Block\Template\Context'),
    array('_coreDir', 'Magento\Sales\Model\Order\Pdf\AbstractItems', '_rootDirectory'),
    array('_dir', 'Magento\AdvancedCheckout\Model\Import', '_filesystem'),
    array('_dir', 'Magento\Backup\Helper\Data'),
    array('_dir', 'Magento\Backup\Model\Observer', '_filesystem'),
    array('_dir', 'Magento\Catalog\Model\Category\Attribute\Backend\Image', '_filesystem'),
    array('_dir', 'Magento\Catalog\Model\Resource\Product\Attribute\Backend\Image', '_filesystem'),
    array('_dir', 'Magento\CatalogEvent\ModelEvent', '_filesystem'),
    array('_dir', 'Magento\Cms\Helper\Wyiswig\Images'),
    array('_dir', 'Magento\Email\Model\Template'),
    array('_dir', 'Magento\CatalogImportExport\Model\Import\Product', '_mediaDirectory'),
    array('_dir', 'Magento\ImportExport\Model\AbstractModel', '_varDirectory'),
    array('_coreDir', 'Magento\Install\Model\Installer\Console'),
    array('_dir', 'Magento\Install\Model\Installer\Filesystem'),
    array('_coreDir', 'Magento\Paypal\Model\Report\Settlement', '_filesystem'),
    array('_applicationDirs', 'Magento\Widget\Model\Config\FileResolver', '_filesystem'),
    array('_formFields', 'Magento\Framework\View\Element\Redirect', 'formFields'),
    array('_formFactory', 'Magento\Framework\View\Element\Redirect', 'formFactory'),
    array('_dispersion', 'Magento\Theme\Block\Html\Pager'),
    array('_assets', 'Magento\Framework\View\Asset\Collection', 'assets'),
    array('_objectManager', 'Magento\Framework\View\Asset\GroupedCollection', 'objectManager'),
    array('_groups', 'Magento\Framework\View\Asset\GroupedCollection', 'groups'),
    array('_objectManager', 'Magento\Framework\View\Asset\MergeService', 'objectManager'),
    array('_scopeConfig', 'Magento\Framework\View\Asset\MergeService', 'config'),
    array('_filesystem', 'Magento\Framework\View\Asset\MergeService', 'filesystem'),
    array('_dirs', 'Magento\Framework\View\Asset\MergeService', 'dirs'),
    array('_state', 'Magento\Framework\View\Asset\MergeService', 'state'),
    array('_strategy', 'Magento\Framework\View\Asset\MergeStrategy\Checksum', 'strategy'),
    array('_filesystem', 'Magento\Framework\View\Asset\MergeStrategy\Checksum', 'filesystem'),
    array('_filesystem', 'Magento\Framework\View\Asset\MergeStrategy\Direct', 'filesystem'),
    array('_dirs', 'Magento\Framework\View\Asset\MergeStrategy\Direct', 'dirs'),
    array('_cssUrlResolver', 'Magento\Framework\View\Asset\MergeStrategy\Direct', 'cssUrlResolver'),
    array('_strategy', 'Magento\Framework\View\Asset\MergeStrategy\FileExists', 'strategy'),
    array('_filesystem', 'Magento\Framework\View\Asset\MergeStrategy\FileExists', 'filesystem'),
    array('_objectManager', 'Magento\Framework\View\Asset\Merged', 'objectManager'),
    array('_logger', 'Magento\Framework\View\Asset\Merged', 'logger'),
    array('_mergeStrategy', 'Magento\Framework\View\Asset\Merged', 'mergeStrategy'),
    array('_assets', 'Magento\Framework\View\Asset\Merged', 'assets'),
    array('_contentType', 'Magento\Framework\View\Asset\Merged', 'contentType'),
    array('_originalAsset', 'Magento\Framework\View\Asset\Minified', 'originalAsset'),
    array('_minifier', 'Magento\Framework\View\Asset\Minified', 'minifier'),
    array('_file', 'Magento\Framework\View\Asset\Minified', 'file'),
    array('_url', 'Magento\Framework\View\Asset\Minified', 'url'),
    array('_viewUrl', 'Magento\Framework\View\Asset\Minified', 'viewUrl'),
    array('_logger', 'Magento\Framework\View\Asset\Minified', 'logger'),
    array('_scopeConfig', 'Magento\Framework\View\Asset\MinifyService', 'сonfig'),
    array('_objectManager', 'Magento\Framework\View\Asset\MinifyService', 'objectManager'),
    array('_enabled', 'Magento\Framework\View\Asset\MinifyService', 'enabled'),
    array('_minifiers', 'Magento\Framework\View\Asset\MinifyService', 'minifiers'),
    array('_dirs', 'Magento\Framework\View\Asset\MinifyService', 'dirs'),
    array('_appState', 'Magento\Framework\View\Asset\MinifyService', 'appState'),
    array('_properties', 'Magento\Framework\View\Asset\PropertyGroup', 'properties'),
    array('_productThumbnail', 'Magento\Checkout\Block\Cart\Item\Renderer'),
    array('_url', 'Magento\Framework\View\Asset\Remote', 'url'),
    array('_contentType', 'Magento\Framework\View\Asset\Remote', 'contentType'),
    array('_frameOpenTag', 'Magento\Framework\View\Element\AbstractBlock'),
    array('_frameCloseTag', 'Magento\Framework\View\Element\AbstractBlock'),
    array('_messagesBlock', 'Magento\Framework\View\Element\AbstractBlock'),
    array('escapeMessageFlag', 'Magento\Framework\View\Block\Messages'),
    array('_handlerFactory', 'Magento\Backend\Block\Widget\Grid\Massaction\Additional'),
    array('_flatResourceFactory', 'Magento\Catalog\Model\Observer'),
    array('_catalogCategoryFlat', 'Magento\Catalog\Model\Observer'),
    array('_catalogCategoryFlat', 'Magento\Catalog\Block\Navigation'),
    array('_catalogCategoryFlat', 'Magento\Catalog\Model\Category'),
    array('_storesRootCategories', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_resourceHelper', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_catalogCategory', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_isRebuilt', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_isBuilt', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_attributeCodes', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_columnsSql', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('_columns', 'Magento\Catalog\Model\Resource\Category\Flat'),
    array('fileIteratorFactory', 'Magento\Core\Model\Theme\Collection'),
    array('_allowDuplication', 'Magento\Framework\View\Publisher'),
    array('_modulesReader', 'Magento\Framework\View\Publisher'),
    array('_directoryUrl', 'Magento\Directory\Block\Currency'),
    array('_pageVarName', 'Magento\Catalog\Block\Product\ProductList\Toolbar'),
    array('_orderVarName', 'Magento\Catalog\Block\Product\ProductList\Toolbar'),
    array('_directionVarName', 'Magento\Catalog\Block\Product\ProductList\Toolbar'),
    array('_modeVarName', 'Magento\Catalog\Block\Product\ProductList\Toolbar'),
    array('_limitVarName', 'Magento\Catalog\Block\Product\ProductList\Toolbar'),
    array('_encryptedSessionId', 'Magento\Framework\Url'),
    ['_tokenRegex', 'Magento\Framework\Translate\Inline'],
    ['_translator', 'Magento\Framework\Translate\Inline'],
    ['_appState', 'Magento\Framework\Translate\Inline'],
    ['_translateInline', 'Magento\Framework\Translate'],
    ['_inlineInterface', 'Magento\Framework\Translate'],
    ['_translateFactory', 'Magento\Framework\Translate'],
    ['_placeholderRender', 'Magento\Framework\Translate'],
    ['_canUseInline', 'Magento\Framework\Translate'],
    ['_eventManager', 'Magento\Framework\Translate'],
    ['_inlineFactory', 'Magento\Framework\App\Helper\Context', 'translateInline'],
    ['_inlineFactory', 'Magento\Framework\App\Helper\AbstractHelper', 'translateInline'],
    ['_storeManager', 'Magento\Translation\Model\Resource\Translate'],
    ['_storeManager', 'Magento\Translation\Model\Resource\String'],
    ['_isVdeRequest', 'Magento\DesignEditor\Helper\Data'],
    ['_translator', 'Magento\Framework\Phrase\Renderer\Translate', 'translator'],
    ['_translator', 'Magento\Core\Model\Validator\Factory'],
    ['_configFactory', 'Magento\Core\Model\App\Emulation', 'inlineConfig'],
    ['_scopeConfig', 'Magento\Translation\Model\Inline\Config', 'config'],
    ['_translate', 'Magento\Directory\Model\Observer'],
    ['_translate', 'Magento\Newsletter\Model\Subscriber'],
    ['_translate', 'Magento\Sendfriend\Model\Sendfriend'],
    ['_translateModel', 'Magento\Sitemap\Model\Observer'],
    ['_translator', 'Magento\Checkout\Helper\Data'],
    ['_translate', 'Magento\GiftRegistry\Model\Entity'],
    ['_translate', 'Magento\Log\Model\Cron'],
    ['_translate', 'Magento\ProductAlert\Model\Observer'],
    ['translate', 'Magento\Reminder\Model\Rule'],
    ['_translate', 'Magento\Rma\Model\Rma'],
    ['_translate', 'Magento\Rma\Model\Rma\Status\History'],
    ['_translate', 'Magento\Sales\Model\Order\Pdf\AbstractPdf'],
    ['_layout', 'Magento\Install\App\Action\Plugin\Design'],
    ['_layout', 'Magento\Framework\View\DesignLoader'],
    ['_area', 'Magento\Framework\View\Layout'],
    ['_coreData', '\Magento\Rss\Block\Catalog\Special', 'priceCurrency'],
    ['_tierPriceDefaultTemplate', 'Magento\Catalog\Block\Product\AbstractProduct'],
    ['_mimeTypes', 'Magento\Framework\File\Transfer\Adapter\Http', '\Magento\Framework\File\Mime::$mimeTypes'],
    ['_viewFileResolution', 'Magento\Framework\View\FileSystem', '_fileResolution, _localeFileResolution'],
    ['_inventoryModel', 'Magento\AdvancedCheckout\Model\Resource\Sku\Errors\Grid\Collection'],
    ['_productInstance', 'Magento\CatalogInventory\Model\Stock\Item'],
    ['_scopeConfig', 'Magento\CatalogInventory\Helper\Minsaleqty', 'scopeConfig'],
    ['_stopFurtherRules', 'Magento\SalesRule\Model\Validator'],
    ['_usageFactory', 'Magento\SalesRule\Model\Validator', 'Magento\SalesRule\Model\Validator\Utility'],
    ['_couponFactory', 'Magento\SalesRule\Model\Validator', 'Magento\SalesRule\Model\Validator\Utility'],
    ['_customerFactory', 'Magento\SalesRule\Model\Validator', 'Magento\SalesRule\Model\Validator\Utility'],
    ['_skipModuleUpdate', '\Magento\Framework\Module\Updater'],
    ['_factory', 'Magento\Framework\Module\Updater'],
    ['_resourceList', 'Magento\Framework\Module\Updater'],
    ['_storeManager', 'Magento\Customer\Controller\Account'],
    ['_urlFactory', 'Magento\Customer\Controller\Account'],
    ['_addressHelper', 'Magento\Customer\Controller\Account', 'Magento\Customer\Controller\Account\Confirm::$addressHelper'],
    ['_scopeConfig', 'Magento\Customer\Controller\Account'],
    ['_customerAccountService', 'Magento\Customer\Controller\Account'],
    ['_formFactory', 'Magento\Customer\Controller\Account\CreatePost', 'Magento\Customer\Controller\Account\CreatePost::formFactory'],
    ['_subscriberFactory', 'Magento\Customer\Controller\Account\CreatePost', 'Magento\Customer\Controller\Account\CreatePost::subscriberFactory'],
    ['_regionBuilder', 'Magento\Customer\Controller\Account\CreatePost', 'Magento\Customer\Controller\Account\CreatePost::regionBuilder'],
    ['_addressBuilder', 'Magento\Customer\Controller\Account\CreatePost', 'Magento\Customer\Controller\Account\CreatePost::addressBuilder'],
    ['_customerDetailsBuilder', 'Magento\Customer\Controller\Account\CreatePost', 'Magento\Customer\Controller\Account\CreatePost::customerDetailsBuilder'],
    ['_customerBuilder', 'Magento\Customer\Controller\Account\Edit', 'Magento\Customer\Controller\Account\Edit::customerBuilder'],
    ['_customerBuilder', 'Magento\Customer\Controller\Account\EditPost', 'Magento\Customer\Controller\Account\EditPost::customerBuilder'],
    ['_customerDetailsBuilder', 'Magento\Customer\Controller\Account\EditPost', 'Magento\Customer\Controller\Account\EditPost::customerDetailsBuilder'],
    ['_formKeyValidator', 'Magento\Customer\Controller\Account\EditPost', 'Magento\Customer\Controller\Account\EditPost::formKeyValidator'],
    ['_customerHelperData', 'Magento\Customer\Controller\Account\LoginPost', 'Magento\Customer\Controller\Account\LoginPost::customerHelperData'],
    ['_formKeyValidator', 'Magento\Customer\Controller\Account\LoginPost', 'Magento\Customer\Controller\Account\LoginPost::formKeyValidator'],
    ['_openActions', 'Magento\Customer\Controller\Account', 'Magento\Customer\Controller\Account::openActions'],
    ['_session', 'Magento\Customer\Controller\Account', 'Magento\Customer\Controller\Account::session'],
    ['_cache', 'Magento\Framework\App\Magento\Framework\App\Resource', 'Magento\Framework\App\Resource\ConnectionFactory'],
    ['_debug', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'logger'],
    ['_logQueryTime', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\LoggerAbstract'],
    ['_logAllQueries', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\LoggerAbstract'],
    ['_logCallStack', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\LoggerAbstract'],
    ['_debugFile', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\File'],
    ['_filesystem', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\File'],
    ['_debugTimer', 'Magento\Framework\DB\Adapter\Pdo\Mysql', 'Magento\Framework\DB\Logger\LoggerAbstract'],
    ['_resourceName', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_resourceName'],
    ['_moduleConfig', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_moduleConfig'],
    ['_callAfterApplyAllUpdates', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_callAfterApplyAllUpdates'],
    ['_setupCache', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_setupCache'],
    ['_modulesReader', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_modulesReader'],
    ['_eventManager', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_eventManager'],
    ['_logger', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_logger'],
    ['_resource', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_resource'],
    ['_migrationFactory', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::_migrationFactory'],
    ['filesystem', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::filesystem'],
    ['modulesDir', 'Magento\Framework\Module\Setup', 'Magento\Framework\Module\DataSetup::modulesDir'],
    ['_directoryData', 'Magento\Customer\Model\Attribute\Data\Postcode', 'Magento\Customer\Model\Attribute\Data\Postcode::directoryHelper'],
    ['_conditionModels', 'Magento\Rule\Model\Condition\Combine'],
    ['_lables', 'Magento\SalesRule\Model\Rule'],
    ['_catalogData', 'Magento\Catalog\Block\Product\AbstractProduct'],
);
