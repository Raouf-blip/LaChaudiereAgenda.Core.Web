<?php

namespace Chaudiere\core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Chaudiere\core\domain\entities\Events;

class Images extends Model {

    public $timestamps = false;
    protected $table = 'images';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'name'];

    public function events() {
        return $this->hasMany(Events::class, 'image_id', 'id');
    }
}
