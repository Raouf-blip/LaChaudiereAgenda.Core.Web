<?php

namespace Chaudiere\core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class Events extends Model {

    protected $table = 'events';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title', 'description', 'start_date', 'end_date',
        'start_time', 'end_time', 'price',
        'image_id', 'category_id', 'created_by', 'is_published'
    ];

    public function category() {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }
}
