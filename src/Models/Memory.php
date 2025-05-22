<?php

namespace BuildWithLaravel\Ensemble\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static Builder for($memoryable)
 */
class Memory extends Model
{
    use HasUuids;

    protected $table = 'ensemble_memories';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'memoryable_id',
        'memoryable_type',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function memoryable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeFor(Builder $builder, $memoryable): Builder
    {
        return $builder->whereMorphedTo('memoryable', $memoryable);
    }
}
