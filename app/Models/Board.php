<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    /**
     * @var mixed|null
     */
    protected $fillable = ["letter", "value", "gameId", "x", "y"];
}
