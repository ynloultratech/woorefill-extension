<?php

namespace WooRefill\Tests;

/**
 * Class PluginInitializationTest
 */
class PluginInitializationTest extends AbstractBasePluginTest
{
    public function testPluginInitialization()
    {
        $logger = $this->get('logger');

        self::assertInstanceOf('WooRefill\App\Logger\Logger', $logger);
        self::assertEquals(APIKEY, $this->getParameter('api_key'));
    }
}