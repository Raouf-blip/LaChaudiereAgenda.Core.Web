<?php

namespace Chaudiere\entities;

use Illuminate\Database\Eloquent\Model;
use Chaudiere\entities\Events;

class Categories extends Models {

    protected $table = 'categories';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $keyType = 'string';

    public function events() {
        return $this->hasMany(Events::class, 'id', 'name');
    }
}