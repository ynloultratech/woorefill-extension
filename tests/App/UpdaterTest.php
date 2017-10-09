<?php

namespace WooRefill\Tests\App;

use WooRefill\App\Updater;
use WooRefill\Tests\AbstractBasePluginTest;

class UpdaterTest extends AbstractBasePluginTest
{

    public function testSetTransient()
    {
        /** @var Updater $updater */
        $updater = $this->get('updater');

        $this->mockGetRepoReleaseInfo();

        $transient = new \stdClass();
        $transient->checked = false;

        $transient = $updater->setTransient($transient);
        self::assertEquals('1.0.4', $transient->response['woorefill-extension/woorefill.php']->new_version);
        self::assertEquals('https://github.com/ynloultratech/woorefill-extension', $transient->response['woorefill-extension/woorefill.php']->url);
        self::assertEquals('https://github.com/ynloultratech/woorefill-extension/releases/download/1.0.4/woorefill-extension.zip', $transient->response['woorefill-extension/woorefill.php']->package);
    }

    public function testSetPluginInfo()
    {
        /** @var Updater $updater */
        $updater = $this->get('updater');
        $this->mockGetRepoReleaseInfo();

        $response = new \stdClass();
        $response->slug = $this->getPluginSlug();

        $response = $updater->setPluginInfo(null, null, $response);

        self::assertEquals('1.0.4', $response->version);
        self::assertEquals($this->getPluginSlug(), $response->slug);
        self::assertEquals('2016-11-30T03:27:08Z', $response->last_updated);
        self::assertEquals('WooRefill', $response->plugin_name);
        self::assertEquals('1.0.4', $response->version);
        self::assertEquals('YnloUltratech', $response->author);
        self::assertEquals('4.1', $response->requires);
        self::assertEquals('4.2', $response->tested);
        self::assertEquals('https://github.com/ynloultratech/woorefill-extension', $response->homepage);
        self::assertEquals('https://github.com/ynloultratech/woorefill-extension/releases/download/1.0.4/woorefill-extension.zip', $response->download_link);
        self::assertEquals('WooRefill is a extension for WooCommerce to add wireless plans to your shop and do refills. By YnloUltratech.', $response->sections['description']);
        self::assertContains('<p>fix error 500 when import empty carriers </p>', $response->sections['changelog']);
    }

    public function testPostInstall()
    {
        /** @var Updater $updater */
        $updater = $this->get('updater');
        $this->mockGetRepoReleaseInfo();

        $fileSystemMock = $this->getMockBuilder('\WP_Filesystem_Base')->getMock();
        $pluginFolder = WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.dirname($this->getPluginSlug());

        self::getMockery()->shouldReceive('is_plugin_active')->with($this->getPluginSlug())->andReturn(true)->once();
        self::getMockery()->shouldReceive('activate_plugin')->with($this->getPluginSlug())->once();

        $result = ['destination' => $pluginFolder];
        $result = $updater->postInstall(true, null, $result);

        self::assertEquals($pluginFolder, $result['destination']);
    }

    protected function mockGetRepoReleaseInfo()
    {
        self::getMockery()->shouldReceive('plugin_basename')->with($this->getPluginFile())->andReturn($this->getPluginSlug())->once();
        self::getMockery()->shouldReceive('get_plugin_data')->with($this->getPluginFile())->andReturn($this->getPluginData())->once();

        $url = "https://api.github.com/repos/ynloultratech/woorefill-extension/releases/latest";

        $response = ['body' => $this->getGithubAPIJson()];
        self::getMockery()->shouldReceive('wp_remote_get')->with($url, ['timeout' => 60])->andReturn($response)->once();
    }

    protected function getPluginSlug()
    {
        return 'woorefill-extension/woorefill.php';
    }

    protected function getPluginFile()
    {
        return realpath(__DIR__.'/../../woorefill.php');
    }

    protected function getPluginData()
    {
        return [
            'Name' => 'WooRefill',
            'PluginURI' => 'https://github.com/ynloultratech/woorefill-extension',
            'Version' => '1.0.3',
            'Description' => 'WooRefill is a extension for WooCommerce to add wireless plans to your shop and do refills. By YnloUltratech.',
            'Author' => 'YnloUltratech',
            'AuthorURI' => '',
            'TextDomain' => 'woorefill-extension',
            'DomainPath' => '',
            'Network' => false,
            'Title' => 'WooRefill',
            'AuthorName' => 'YnloUltratech',
        ];
    }

    protected function getGithubAPIJson()
    {
        return file_get_contents(__DIR__.'/../Fixtures/github_api_release.json');
    }
}
