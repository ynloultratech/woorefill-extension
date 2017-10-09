<?php

namespace WooRefill\Tests\Shop;

use WooRefill\Shop\Cart;
use WooRefill\Tests\AbstractBasePluginTest;

class CartTestAbstractBase extends AbstractBasePluginTest
{
    public function testAddToCart()
    {
        /** @var Cart $cart */
        $cart = $this->get('shop_cart');

        $productMock = $this->getMockBuilder('\WC_Product_Wireless')->disableOriginalConstructor()->getMock();
        $productMock->expects(self::once())->method('is_purchasable')->willReturn(true);
        $productMock->expects(self::once())->method('is_in_stock')->willReturn(true);
        $productMock->expects(self::once())->method('get_id')->willReturn(1);
        $productMock->expects(self::once())->method('add_to_cart_text')->willReturn('Refill');

        self::getMockery()->shouldReceive('do_action')->with('woocommerce_before_add_to_cart_form')->once();
        self::getMockery()->shouldReceive('do_action')->with('woocommerce_before_add_to_cart_button')->once();
        self::getMockery()->shouldReceive('do_action')->with('woocommerce_after_add_to_cart_button')->once();
        self::getMockery()->shouldReceive('do_action')->with('woocommerce_after_add_to_cart_form')->once();
        self::getMockery()->shouldReceive('wc_get_checkout_url')->andReturnValues(['/checkout'])->once();

        $GLOBALS['product'] = $productMock;
        ob_start();
        $cart->addToCart();
        $result = ob_get_clean();

        self::assertContains('<button type="submit" class="single_add_to_cart_button button alt">Refill</button>', $result);
        self::assertContains('<input type="hidden" name="add-to-cart" value="1"/>', $result);
        self::assertContains('<form action="/checkout" class="cart" method="post" enctype=\'multipart/form-data\'>', $result);
    }
}
