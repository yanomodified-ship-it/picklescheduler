<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Not "settings": that table name is already used in your database
    protected $table = 'pricing_settings';

    protected $fillable = ['key', 'value'];

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}