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

namespace WooRefill\Tests\Shop;

use WooRefill\App\Model\LocalProduct;
use WooRefill\App\Model\Product;
use WooRefill\Shop\Checkout;
use WooRefill\Tests\AbstractBasePluginTest;

class CheckoutTest extends AbstractBasePluginTest
{

    public function testUpdateOrderMeta()
    {
        $cart = self::getMockBuilder('WooRefill\Shop\Cart')->disableOriginalConstructor()->getMock();
        $cart->expects(self::once())->method('hasWirelessProduct')->willReturn(true);

        $_POST = [
            '_woo_refill_meta_phone' => '+1 (305) 123-1234',
            '_woo_refill_meta_amount' => 20,
        ];

        /** @var Checkout|\PHPUnit_Framework_MockObject_MockObject $checkout */
        $checkout = self::getMockBuilder('WooRefill\Shop\Checkout')->disableOriginalConstructor()->setMethods(
            [
                'getCart',
            ]
        )->getMock();
        $checkout->expects(self::once())->method('getCart')->willReturn($cart);

        self::getMockery()->shouldReceive('update_post_meta')->withArgs([1, '_woo_refill_meta_phone', '+13051231234'])->once();
        self::getMockery()->shouldReceive('update_post_meta')->withArgs([1, '_woo_refill_meta_amount', 20])->once();

        $checkout->updateOrderMeta(1);
    }

    public function testValidatePostedData()
    {
        $product = self::getMockBuilder('\WC_Product')->disableOriginalConstructor()->getMock();
        $product->expects(self::once())->method('get_id')->willReturn(1);

        $cart = self::getMockBuilder('WooRefill\Shop\Cart')->disableOriginalConstructor()->getMock();
        $cart->expects(self::once())->method('getFirstWirelessProduct')->willReturn($product);

        $productManager = self::getMockBuilder('WooRefill\App\EntityManager\ProductManager')->disableOriginalConstructor()->getMock();
        $localProduct = new LocalProduct();
        $localProduct->id = 1;
        $localProduct->sku = 123;
        $productManager->expects(self::once())->method('find')->willReturn($localProduct);

        $productsApi = self::getMockBuilder('WooRefill\App\Api\ProductsEndpoint')->disableOriginalConstructor()->getMock();
        $remoteProduct = new Product();
        $remoteProduct->localProduct = $localProduct;
        $remoteProduct->id = 123;
        $remoteProduct->inputs = [
            'phone' => [
                'label' => 'Phone',
                'required' => true,
                'validationRegex' => [
                    '/^\\+1\\d{10,10}$/' => 'Invalid phone number',
                ],
            ],
            'contact_phone' => [
                'label' => 'Phone',
                'required' => true,
                'validationRegex' => [
                    '/^\\+1\\d{10,10}$/' => 'Invalid phone number',
                ],
            ],
            'web' => [
                'label' => 'Web',
                'required' => false,
                'validationRegex' => [
                    '/dd\/' => 'Invalid phone number', //invalid regex
                ],
            ],
            'amount' => [
                'label' => 'Amount',
                'required' => true,
            ],
            'email' => [
                'label' => 'Email',
                'required' => true,
            ],
        ];
        $productsApi->expects(self::once())->method('get')->with(123)->willReturn($remoteProduct);

        $api = self::getMockBuilder('WooRefill\App\Api\WooRefillApi')->disableOriginalConstructor()->getMock();
        $api->expects(self::once())->method('getProducts')->willReturn($productsApi);

        /** @var Checkout|\PHPUnit_Framework_MockObject_MockObject $checkout */
        $checkout = self::getMockBuilder('WooRefill\Shop\Checkout')->disableOriginalConstructor()->setMethods(
            [
                'getCart',
                'getProductManager',
                'getRefillAPI',
                'getLogger',
            ]
        )->getMock();

        $logger = self::getMockBuilder('WooRefill\App\Logger\Logger')->disableOriginalConstructor()->getMock();
        $logger->expects(self::once())->method('error')->with('Validation expression error: preg_match(): No ending delimiter \'/\' found');

        $checkout->expects(self::once())->method('getCart')->willReturn($cart);
        $checkout->expects(self::once())->method('getProductManager')->willReturn($productManager);
        $checkout->expects(self::once())->method('getRefillAPI')->willReturn($api);
        $checkout->expects(self::once())->method('getLogger')->willReturn($logger);

        $data = [
            '_woo_refill_meta_phone' => '+13051231234',
            '_woo_refill_meta_contact_phone' => '23323',
            '_woo_refill_meta_amount' => 20,
        ];

        /** @var \WP_Error|\PHPUnit_Framework_MockObject_MockObject $errors */
        $errors = self::getMockBuilder('WP_Error')->setMethods(['add'])->disableOriginalConstructor()->getMock();
        $errors->expects(self::exactly(2))
               ->method('add')
               ->withConsecutive(
                   ['validation', 'Invalid phone number'],
                   ['validation', '<strong>Email</strong> is a required field']
               );

        $checkout->validatePostedData($data, $errors);
    }
}