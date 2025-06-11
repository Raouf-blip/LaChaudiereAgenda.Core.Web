<?php

namespace Chaudiere\core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Chaudiere\core\domain\entities\Events;

class Images extends Model {

    protected $table = 'images';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $keyType = "string";

    public function events() {
        return $this->hasMany(Events::class, 'image_id', 'id');
    }
}
