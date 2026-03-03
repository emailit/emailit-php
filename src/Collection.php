<?php

namespace Emailit;

/**
 * Paginated list of API resources.
 *
 * @property string|null $next_page_url
 * @property string|null $previous_page_url
 */
class Collection extends EmailitObject implements \Countable, \IteratorAggregate
{
    public function getData(): array
    {
        return $this->_values['data'] ?? [];
    }

    public function count(): int
    {
        return count($this->getData());
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getData());
    }

    public function hasMore(): bool
    {
        return $this->_values['next_page_url'] !== null;
    }
}
