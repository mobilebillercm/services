<?php
/**
 * Created by PhpStorm.
 * Admin: nkalla
 * Date: 13/09/18
 * Time: 18:21
 */

namespace App\domain\model;


use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $table = 'services';
    protected $fillable = ['b_id', 'name', 'description', 'icon', 'active', 'created_by', 'created_at', 'updated_at'];

    public function __construct($b_id = null, $name = null, $description = null, $icon = null, $active = null, $created_by = null,
                                array $attributes = []){
        parent::__construct($attributes);
        $this->b_id = $b_id;
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->active = $active;
        $this->created_by = $created_by;
    }

    public function isInsertable(){
        $selectedServicesByBid = Service::where('b_id', '=', $this->b_id)->get();
        if (count($selectedServicesByBid) > 0){
            return [false, 'Erreur: Duplication de service'];
        }
        $selectedServicesByName = Service::where('name', '=', $this->name)->get();
        if (count($selectedServicesByName) > 0){
            return [false, 'Erreur: Duplication de service'];
        }

        $selectedAdminByBid = Admin::where('b_id', '=', $this->created_by)->get();

        if (!(count($selectedAdminByBid) ===1)){
            return [false, 'Erreur: Administrateur Fictif'];
        }

        return [true,'Valide'];
    }



}