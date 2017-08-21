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

namespace WooRefill\App\Api;

use WooRefill\App\Model\Transaction;

class TransactionsEndpoint extends AbstractEndpoint
{
    /**
     * @inheritDoc
     */
    public function __construct(WooRefillApi $api)
    {
        parent::__construct($api, '/transactions');
    }

    public function post(Transaction $transaction)
    {
        $body = $this->api->getSerializer()->serialize($transaction, 'json');
        $transaction = $this->requestPost('', $body);

        if ($transaction) {
            /** @var Transaction $transaction */
            $transaction = $this->deserialize($transaction, $this->getModeClass());

            if ($transaction && $transaction->status !== 'ERROR') {
                $this->api->getLogger()->addLog('The order #%s has been processed successfully.', $transaction->correlationId);

                return $transaction;
            }

            $message = sprintf('Error (%s) - %s', $transaction->errorCode, $transaction->responseMessage);
            $this->api->getLogger()->addErrorLog($message);
            throw new \Exception($message);
        }

        throw new \RuntimeException('Invalid API response');
    }

    protected function getModeClass()
    {
        return 'WooRefill\App\Model\Transaction';
    }
}