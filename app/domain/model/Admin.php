<?php
/**
 * Created by PhpStorm.
 * Admin: nkalla
 * Date: 13/09/18
 * Time: 20:20
 */

namespace App\domain\model;


use Illuminate\Database\Eloquent\Model;

class Admin extends  Model
{

    protected $table = 'admins';
    protected $fillable = ['b_id','tenant', 'firstname', 'lastname', 'email', 'created_at', 'updated_at'];

    public function __construct($b_id = null, $tenant = null, $firstname = null, $lastname = null, $email = null, array $attributes = []){
        parent::__construct($attributes);
        $this->b_id = $b_id;
        $this->tenant = $tenant;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }

}