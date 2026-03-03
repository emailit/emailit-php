<?php

namespace Emailit\Service;

use Emailit\BaseEmailitClient;

abstract class AbstractServiceFactory
{
    private BaseEmailitClient $client;

    /** @var array<string, AbstractService|AbstractServiceFactory> */
    private array $services = [];

    public function __construct(BaseEmailitClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return array<string, class-string>
     */
    abstract protected function getServiceMap(): array;

    public function __get(string $name): AbstractService|AbstractServiceFactory|null
    {
        return $this->getService($name);
    }

    public function getService(string $name): AbstractService|AbstractServiceFactory|null
    {
        $map = $this->getServiceMap();

        if (!isset($map[$name])) {
            return null;
        }

        if (!isset($this->services[$name])) {
            $this->services[$name] = new $map[$name]($this->client);
        }

        return $this->services[$name];
    }
}
