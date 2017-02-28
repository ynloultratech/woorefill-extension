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

namespace WooRefill\Admin\Controller;

use WooRefill\Admin\Form\ImportForm;
use WooRefill\Admin\Import\ProductImporter;
use WooRefill\App\Api\RefillAPI;
use WooRefill\App\Controller\Controller;
use WooRefill\GuzzleHttp\Exception\ClientException;
use WooRefill\Symfony\Component\Form\FormError;
use WooRefill\Symfony\Component\HttpFoundation\Response;

/**
 * Class ImportProductsController
 */
class ImportProductsController extends Controller
{
    /**
     * importAction
     */
    public function importAction()
    {
        $submitted = false;
        $carriers = [];
        if ($this->getRequest()->isMethod('post')) {
            $form = $this->buildImportForm();
            $form->handleRequest($this->getRequest());
            $submitted = true;
            if ($form->isValid()) {
                try {
                    /** @var ProductImporter $importer */
                    $importer = $this->get('product_importer');
                    $importer->import($form->getData());
                    $success = 'All selected products has been imported successfully.';
                    $submitted = false;
                } catch (\Exception $e) {
                    $form->addError(new FormError($e->getMessage()));
                }
                $carriers = $this->getApi()->getCarriers();
            }
        }

        $this->render(
            '@Admin/import/import.html.twig',
            [
                'submitted' => $submitted,
                'carriers' => $carriers,
                'form' => isset($form) ? $form->createView() : null,
                'success' => isset($success) ? $success : null,
            ]
        );
    }

    public function carriersAction()
    {
        try {
            $carriers = $this->getApi()->getCarriers();
            $this->renderAjax('@Admin/import/carriers.html.twig', ['carriers' => $carriers]);
        } catch (\Exception $e) {
            $this->showError($e);
        }
    }

    public function productsAction()
    {
        try {
            $this->renderAjax(
                '@Admin/import/products.html.twig',
                [
                    'form' => $this->buildImportForm()->createView(),
                ]
            );
        } catch (ClientException $e) {
            $this->showError($e);
        }
    }

    public function downloadCSVAction()
    {
        $carrierId = $this->getRequest()->get('carrier_id');
        $products = $this->getApi()->getProducts($carrierId);
        $csv = [];
        $csv[] = ['ID', 'Name', 'Type', 'Discount', 'Min Price', 'Max Price', 'Price'];
        foreach ($products as $product) {
            $csv[] = [
                $product->id,
                $product->name,
                $product->type,
                $product->discount_rate,
                $product->variable_amount ? number_format($product->min_amount, 2) : null,
                $product->variable_amount ? number_format($product->max_amount, 2) : null,
                !$product->variable_amount ? number_format($product->amount, 2) : number_format($product->max_amount, 2),
            ];
        }

        $rawCsv = '';
        foreach ($csv as $csvLine) {
            foreach ($csvLine as $column) {
                $rawCsv .= "\"$column\",";
            }
            $rawCsv .= "\n";
        }
        $response = new Response(
            $rawCsv, 200, [
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="products.csv"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
        //clean wp
        ob_clean();
        $response->send();
        exit;
    }

    protected function buildImportForm()
    {
        return $this->createFormBuilder('import_products_form')->getForm();
    }

    /**
     * @return RefillAPI
     */
    protected function getApi()
    {
        return $this->container->get('refill_api');
    }

    protected function showError(\Exception $e)
    {
        $errorMessage = $e->getMessage();
        if ($e instanceof ClientException && $e->getResponse() && $rawJson = $e->getResponse()->getBody()) {
            $json = json_decode($rawJson);
            if ($json->error) {
                $errorMessage = sprintf('The Refill API return the following error (%s).', $json->error->message);
                $errorCode = $e->getCode();
                if ($errorCode > 400 && $errorCode < 500) {
                    $errorMessage .= ' Ensure you have a valid API key configured in your settings.';
                    $errorMessage .= ' <a href="/wp-admin/admin.php?page=wc-settings&tab=wireless">Go to Settings</a>';
                }
            }
        }
        $this->get('logger')->addErrorLog($e);
        $this->renderAjax('@Admin/error_message.html.twig', ['error_message' => $errorMessage]);
    }
}