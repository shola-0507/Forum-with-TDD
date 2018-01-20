<?php

namespace App\Inspections;

use Illuminate\Database\Eloquent\Model;

use Exception;

class Spam extends Model
{

    protected $inspections = [

        InvalidKeywords::Class,
        KeyHeldDown::Class
    ];

    public function detect($body) {

        foreach($this->inspections as $inspection) { 

            app($inspection)->detect($body);
        }

    	return false;
    }

}
