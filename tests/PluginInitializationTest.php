<?php

namespace WooRefill\Tests;

class PluginInitializationTest extends AbstractBasePluginTest
{
    public function testPluginInitialization()
    {
        $logger = $this->get('logger');

        self::assertInstanceOf('WooRefill\App\Logger\Logger', $logger);
        self::assertEquals(APIKEY, $this->getParameter('api_key'));
    }
}