<?php

declare(strict_types = 1);

namespace Amondar\Postman\Route;

use Amondar\ClassAttributes\Results\DiscoveredTarget;
use Amondar\Postman\Attributes\PostmanFormData;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

/**
 * Class RouteAction
 *
 * @author Amondar-SO
 */
final readonly class RouteAction
{
    public ?string $controller;

    public ?string $method;

    public ?Closure $closure;

    public ?string $randomID;

    /**
     * RouteAction constructor.
     */
    public function __construct(
        ?string $controller = null,
        ?string $method = null,
        ?Closure $closure = null
    ) {
        if ($closure !== null) {
            $controller = null;
            $method = null;

            $this->randomID = Str::random(20);
        } else {
            $this->randomID = null;
        }

        if (
            $closure === null && ($controller === null || $method === null)
        ) {
            throw new InvalidArgumentException('Set controller + method or closure.');
        }

        $this->controller = $controller;
        $this->method = $method;
        $this->closure = $closure;
    }

    public function getActionFcn(): string
    {
        return $this->closure ? 'Closure-' . $this->randomID : $this->controller . '@' . $this->method;
    }

    /**
     * @param  Collection<int, DiscoveredTarget<object, PostmanFormData>>  $vocabulary
     */
    public function getFormData(Collection $vocabulary): array
    {
        try {
            return match (true) {
                $this->controller !== null => $this->getFormDataOnClass($this->controller, $this->method, $vocabulary),
                $this->closure !== null    => $this->getFormDataOnClosure($vocabulary),
                default                    => [],
            };
        } catch (ReflectionException $e) {
            return [];
        }
    }

    /**
     * @param  string  $class  The fully qualified name of the class for which form data is being retrieved.
     * @param  string|null  $method  The method name, if applicable, for which specific form data retrieval is attempted.
     * @param  Collection<int, DiscoveredTarget<object, PostmanFormData>>  $vocabulary  A collection of discovered targets containing metadata and form data mappings.
     *
     * @throws ReflectionException If the method or class reflection fails during processing.
     */
    private function getFormDataOnClass(string $class, ?string $method, Collection $vocabulary): array
    {
        /** @var DiscoveredTarget<object, PostmanFormData>|null $discovery */
        $discovery = $vocabulary->where('target', $class)->first();

        // If the target class has a PostmanFormData annotation, return its contents.
        if ($discovery?->onClass->isNotEmpty()) {
            return $discovery->onClass->first()->render();
        }

        // If a specific method was requested, attempt to retrieve form data based on that method.
        if ($method !== null) {
            // If the target class has a PostmanFormData annotation for the specified method, return its contents.
            if (
                $discovery?->onMethods->isNotEmpty()
                // @phpstan-ignore-next-line
                && $attribute = $discovery?->onMethods->where('target', $method)->first()?->attributes[0]
            ) {
                return $attribute->render();
            }

            // Otherwise, attempt to retrieve form data based on the parameters of the method that can be instantiated as classes.
            return $this->getFormDataOnMethodParameters($class, $method, $vocabulary);
        }

        return [];
    }

    /**
     * Retrieves form data based on the parameters of a specified method within a class.
     *
     * @param  string  $class  The fully qualified class name containing the method.
     * @param  string  $method  The name of the method whose parameters are being inspected.
     * @param  Collection  $vocabulary  A collection of vocabulary data used to retrieve form data.
     *
     * @throws ReflectionException
     */
    private function getFormDataOnMethodParameters(string $class, string $method, Collection $vocabulary): array
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($method);

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $data = $this->getFormDataOnClass($type->getName(), null, $vocabulary);

                if ($data !== []) {
                    return $data;
                }
            }
        }

        return [];
    }

    /**
     * Retrieves form data based on the parameters of a closure.
     *
     * @param  Collection  $vocabulary  A collection of vocabulary data used to retrieve form data.
     *
     * @throws ReflectionException
     */
    private function getFormDataOnClosure(Collection $vocabulary): array
    {
        $reflection = new ReflectionClosure($this->closure);

        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $data = $this->getFormDataOnClass($type->getName(), null, $vocabulary);

                if ($data !== []) {
                    return $data;
                }
            }
        }

        return [];
    }
}
