<?php

declare(strict_types = 1);

namespace Amondar\Postman\Blueprints;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Class Item
 *
 * @author Amondar-SO
 */
final readonly class Item implements Arrayable
{
    /**
     * Item constructor.
     *
     * @param  Collection<int, Item | Request>  $items
     */
    public function __construct(
        public string $name,
        public Collection $items
    ) {
        //
    }

    /**
     * Adds a new item with the specified name to the collection and return it.
     *
     * @param  string  $name  The name of the item to be added.
     */
    public function addItem(string $name): Item
    {
        $item = new Item($name, new Collection);

        $this->items->push($item);

        return $item;
    }

    /**
     * Adds a request to the collection of items.
     *
     * @param  Request  $request  The request instance to add.
     */
    public function addRequest(Request $request): Item
    {
        $this->items->push($request);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'item' => $this->items->toArray(),
        ];
    }
}
