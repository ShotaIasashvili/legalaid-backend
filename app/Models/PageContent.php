<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = [
        'page',
        'section',
        'key',
        'value',
        'type',
        'label',
        'sort_order',
    ];

    /**
     * Get all content for a given page as a nested array.
     */
    public static function forPage(string $page): array
    {
        $rows = static::where('page', $page)
            ->orderBy('sort_order')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $section = $row->section ?? '_root';
            if ($row->type === 'json') {
                $result[$section][$row->key] = json_decode($row->value, true);
            } elseif ($row->type === 'boolean') {
                $result[$section][$row->key] = (bool) $row->value;
            } else {
                $result[$section][$row->key] = $row->value;
            }
        }

        return $result;
    }
}
