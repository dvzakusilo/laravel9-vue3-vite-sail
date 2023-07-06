<?php

namespace Domains\Varnish\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Save links from sitemap.
 *
 * @package Domains\Varnish\Models
 */
class VarnishSitemapModel extends Model
{
    use HasFactory;
    /**
     * @var string $table Таблица БД, ассоциированная с моделью.
     */
    protected $table = 'varnish_sitemap';

    /**
     * @var string $primaryKey Первичный ключ таблицы БД.
     */
    protected $primaryKey = 'id';

    protected $fillable = ['url', 'status', 'path', 'content'];
}
