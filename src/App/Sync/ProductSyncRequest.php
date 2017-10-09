<?php
/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

namespace WooRefill\App\Sync;

use WooRefill\App\Kernel;

class ProductSyncRequest extends \WP_Async_Request
{
    protected $name = 'sync_woorefill_products_request';

    protected $syncInterval = HOUR_IN_SECONDS;

    /**
     * @inheritDoc
     */
    protected function handle()
    {
        if (get_transient('_woorefill_products_synchronized') !== false || get_transient('_woorefill_products_synchronizing') !== false) {
            return;
        }

        ini_set('max_execution_time', 120);
        set_transient('_woorefill_products_synchronizing', time(), 120); //max time to wait for sync process

        Kernel::getContainer()->get('product_sync')->sync();

        set_transient('_woorefill_products_synchronized', time(), $this->syncInterval);
        delete_transient('_woorefill_products_synchronizing');
    }
}