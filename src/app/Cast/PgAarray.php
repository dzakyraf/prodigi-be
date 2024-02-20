<?php

namespace App\Cast;

use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Transformers\Transformer;

class PgAarray implements Transformer
{

    /**
     *
     * @param DataProperty $property
     * @param mixed $value
     */
    public function transform(DataProperty $property, mixed $value): mixed
    {
        $_array = explode(',', $value);
        return "{".join(",",$_array)."}";
    }
}
