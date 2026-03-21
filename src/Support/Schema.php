<?php

declare(strict_types = 1);

namespace Amondar\Postman\Support;

use Amondar\Postman\Blueprints\FileInfo;
use Amondar\Postman\Blueprints\Item;
use Amondar\Postman\Blueprints\Request;
use Amondar\Postman\Blueprints\Variable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

/**
 * Class Schema
 *
 * @implements Arrayable<string, mixed>
 *
 * @author Amondar-SO
 */
class Schema implements Arrayable
{
    use Conditionable;

    /**
     * @var Collection<int, Variable>
     */
    protected Collection $variables;

    /**
     * @var Collection<int, Item>
     */
    protected readonly Collection $items;

    public function __construct(protected FileInfo $info)
    {
        $this->items = new Collection;
        $this->variables = new Collection;
    }

    /**
     * Creates a new instance of the class using the given name and optional description.
     *
     * @param  string  $name  The name to initialize the instance with.
     * @param  string|null  $description  An optional description to associate with the instance.
     */
    public static function for(string $name, ?string $description = null): Schema
    {
        return new Schema(new FileInfo($name, $description));
    }

    /**
     * Adds a variable with a specified key and value to the collection.
     *
     * @param  string  $key  The key of the variable.
     * @param  string|int  $value  The value of the variable, which can be a string or an integer.
     */
    public function addVariable(string $key, string|int $value): static
    {
        $this->variables->push(new Variable($key, $value));

        return $this;
    }

    /**
     * Removes a variable from the collection based on the specified key.
     *
     * @param  string  $key  The key of the variable to be removed.
     */
    public function removeVariable(string $key): static
    {
        $this->variables = $this->variables
            ->reject(fn(Variable $variable) => $variable->key === $key)
            ->values();

        return $this;
    }

    /**
     * Replaces an existing variable by removing it and adding a new variable with the given key and value.
     *
     * @param  string  $key  The key of the variable to be replaced.
     * @param  string|int  $value  The new value to associate with the given key.
     */
    public function replaceVariable(string $key, string|int $value): static
    {
        $this->removeVariable($key);

        return $this->addVariable($key, $value);
    }

    /**
     * Adds a request to the destination node within a hierarchical structure defined by a name array.
     *
     * Iterates through a hierarchical path specified by the provided name structure to find or create
     * the appropriate destination node. If the destination node exists, the request is added to it.
     * If it does not exist, the required intermediate nodes and the destination node are created as needed,
     * and the request is then added to the newly created destination node.
     *
     * @param  array  $nameStructure  An array of strings representing the hierarchical path to navigate or create.
     * @param  Request  $request  The request to be added to the target destination node in the structure.
     */
    public function pushRequest(array $nameStructure, Request $request): static
    {
        $parent = new Item('null', $this->items);

        if (count($nameStructure) === 0) {
            $parent->addRequest($request);

            return $this;
        }

        $destination = end($nameStructure);

        foreach ($nameStructure as $segment) {
            $matched = false;

            foreach ($parent->items as $item) {
                if ($item->name === $segment) {
                    $matched = true;

                    $parent = $item;

                    if ($segment === $destination) {
                        $item->addRequest($request);
                    }

                    break;
                }
            }

            if ( ! $matched) {
                $parent = $parent->addItem($segment);

                if ($segment === $destination) {
                    $parent->addRequest($request);
                }
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'variable' => $this->variables->toArray(),
            'info'     => $this->info->toArray(),
            'item'     => $this->items->toArray(),
        ];
    }
}
