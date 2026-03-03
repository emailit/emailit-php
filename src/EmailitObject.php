<?php

namespace Emailit;

class EmailitObject implements \ArrayAccess, \JsonSerializable
{
    /** @var array<string, mixed> */
    protected array $_values = [];

    protected ?ApiResponse $_lastResponse = null;

    public function __construct(array $values = [])
    {
        $this->_values = $values;
    }

    public function __get(string $name): mixed
    {
        return $this->_values[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->_values[$name]);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->_values[$name] = $value;
    }

    public function __unset(string $name): void
    {
        unset($this->_values[$name]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->_values[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->_values[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->_values[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->_values[$offset]);
    }

    public function toArray(): array
    {
        return $this->_values;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function refreshFrom(array $values): static
    {
        $this->_values = $values;

        return $this;
    }

    public function getLastResponse(): ?ApiResponse
    {
        return $this->_lastResponse;
    }

    public function setLastResponse(ApiResponse $response): static
    {
        $this->_lastResponse = $response;

        return $this;
    }
}
