<?php

declare(strict_types = 1);

namespace Amondar\Postman\Attributes;

use Amondar\Postman\Contracts\Renderable;
use Attribute;

/**
 * Class PostmanFormData
 *
 * @template TClass of Renderable
 *
 * @author Amondar-SO
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class PostmanFormData implements Renderable
{
    /**
     * PostmanFormData constructor.
     *
     * @param  array|class-string<TClass>  $data
     */
    public function __construct(public array|string $data)
    {
        //
    }

    public function render(): array
    {
        return is_string($this->data) ? (new $this->data)->render() : $this->data;
    }
}
