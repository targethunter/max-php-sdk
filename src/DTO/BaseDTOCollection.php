<?php

namespace TH\MAX\DTO;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

abstract class BaseDTOCollection implements ArrayAccess, JsonSerializable, IteratorAggregate, \Countable
{
    /**
     * Класс модели
     * @var string
     */
    const ITEM_CLASS = '';

    /**
     * @param mixed $item
     * @return BaseDTO
     */
    protected function checkItem($item): BaseDTO
    {
        $class = static::ITEM_CLASS;
        if (!is_object($item) || !($item instanceof $class)) {
            throw new InvalidArgumentException('Item must be an instance of ' . $class);
        }

        return $item;
    }

    public static function fromArray(array $array): BaseDTOCollection
    {
        $itemClass = static::ITEM_CLASS;

        return self::make(
            array_map(
                function (array $item) use ($itemClass) {
                    /** @var BaseDTO $itemObj */
                    $itemObj = new $itemClass();
                    return $itemObj->fromArray($item);
                },
                $array
            )
        );
    }

    /**
     * @param array $items
     * @return static
     */
    public static function make(array $items): BaseDTOCollection
    {
        $collection = new static();
        foreach ($items as $item) {
            $collection->add($item);
        }

        return $collection;
    }

    /**
     * Хранилище элементов коллекции
     * @var array
     */
    protected $data = [];

    /**
     * @param BaseDTO $value
     * @return $this
     */
    public function add(BaseDTO $value): self
    {
        $this->data[] = $this->checkItem($value);

        return $this;
    }

    /**
     * @param BaseDTO $value
     *
     * @return $this
     */
    public function prepend(BaseDTO $value): self
    {
        array_unshift($this->data, $this->checkItem($value));

        return $this;
    }

    /**
     * @param string|int $offset
     * @param BaseDTO $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $this->checkItem($value);
    }

    /**
     * @param string|int $offset
     * @return BaseDTO|null
     */
    public function offsetGet($offset): ?BaseDTO
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * Get all data
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Получение первого значения
     * @return BaseDTO|null
     */
    public function first(): ?BaseDTO
    {
        $first = reset($this->data);
        if (!$first) {
            $first = null;
        }
        return $first;
    }

    /**
     * Получение последнего значения
     * @return BaseDTO|null
     */
    public function last(): ?BaseDTO
    {
        $last = end($this->data);
        if (!$last) {
            $last = null;
        }
        return $last;
    }

    /**
     * Очистка коллекции
     * @return $this
     */
    public function clear(): self
    {
        $this->data = [];
        return $this;
    }

    /**
     * Удаление элемента из коллекции.
     *
     * @param string|int $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        /** @var BaseDTO $item */
        foreach ($this->data as $key => $item) {
            $result[$key] = $item->toArray();
        }

        return $result;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)json_encode($this->toArray());
    }

    /**
     * @return BaseDTO|null
     */
    public function current(): ?BaseDTO
    {
        return current($this->data);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * @return string|int
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->data) !== null;
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * Проверяет коллекцию на пустоту
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Получение итератора
     *
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function lastKey(): int
    {
        return $this->count() - 1;
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function pluck(string $column): array
    {
        $data = $this->toArray();
        $values = array_column($data, $column);
        if (count($values) !== count($data)) {
            throw new InvalidArgumentException("Some elements missing keys \"{$column}\"");
        }

        return array_combine(array_keys($data), $values);
    }
}