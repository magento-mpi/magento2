<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup();
$this->addConfigField('currency', 'Currency setup');
$this->addConfigField('currency/options', 'Currency options', array('level' => '2', 'show_in_default' => '1'));
$this->addConfigField('currency/options/base', 'Base currency', array('level' => '3',
                                                                      'frontend_type' => 'select',
                                                                      'source_model' => 'adminhtml/system_config_source_currency',
                                                                      'show_in_default' => '1',
                                                                      'show_in_website' => '0',
                                                                      'show_in_store' => '0',
                                                                      'sort_order' => '1',
                                                                )
                    );

$this->addConfigField('currency/options/default', 'Default currency', array('level' => '3',
                                                                      'frontend_type' => 'select',
                                                                      'source_model' => 'adminhtml/system_config_source_currency',
                                                                      'show_in_default' => '1',
                                                                      'show_in_website' => '1',
                                                                      'show_in_store' => '1',
                                                                      'sort_order' => '2',
                                                                )
                   );

$this->addConfigField('currency/options/allow', 'Allowed currencies', array('level' => '3',
                                                                            'frontend_type' => 'multiselect',
                                                                            'source_model' => 'adminhtml/system_config_source_currency',
                                                                            'show_in_default' => '1',
                                                                            'sort_order' => '3',
                                                                        )
                    );


$this->addConfigField('currency/import', 'Import Settings', array('level' => '2', 'show_in_default' => '1'));
$this->addConfigField('currency/import/enabled', 'Enabled', array('level' => '3',
                                                                  'frontend_type' => 'select',
                                                                  'source_model' => 'adminhtml/system_config_source_yesno',
                                                                  'sort_order' => '1',
                                                                  'show_in_default' => '1',
                                                            )
                    );

$this->addConfigField('currency/import/service', 'Service', array('level' => '3',
                                                                  'frontend_type' => 'select',
                                                                  'source_model' => 'adminhtml/system_config_source_currency_service',
                                                                  'sort_order' => '2',
                                                                  'show_in_default' => '1',
                                                            )
                    );

$this->addConfigField('currency/import/time', 'Start Time', array('level' => '3',
                                                                  'frontend_type' => 'time',
                                                                  'sort_order' => '3',
                                                                  'show_in_default' => '1',
                                                            )
                    );

$this->addConfigField('currency/import/frequency', 'Frequency', array('level' => '3',
                                                                  'frontend_type' => 'select',
                                                                  'source_model' => 'adminhtml/system_config_source_cron_frequency',
                                                                  'sort_order' => '4',
                                                                  'show_in_default' => '1',
                                                            )
                    );

$this->addConfigField('currency/import/error_email', 'Notification Email', array('level' => '3',
                                                                  'frontend_type' => 'text',
                                                                  'sort_order' => '5',
                                                                  'show_in_default' => '1',
                                                            )
                    );

$this->endSetup();
