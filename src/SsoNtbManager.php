<?php

namespace Romichoirudin33\Sso;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Romichoirudin33\Sso\Contracts\Factory;
use InvalidArgumentException;
use Romichoirudin33\Sso\NtbProv\NtbProvProvider;

class SsoNtbManager extends Manager implements Factory
{

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->container->make('request'), $config['client_id'],
            $config['client_secret'], $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    protected function createNtbprovDriver()
    {
        $config = $this->config->get('services.ntbprov');

        return $this->buildProvider(
            NtbProvProvider::class, $config
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect ?? '', '/')
            ? $this->container->make('url')->to($redirect)
            : $redirect;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver was specified.');
    }
}
