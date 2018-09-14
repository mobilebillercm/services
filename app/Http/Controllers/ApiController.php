<?php

namespace App\Http\Controllers;

use App\domain\model\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class ApiController extends Controller
{
    public function createService(Request $request){

        //$inputContent = file_get_contents("php://input");
        //$data = json_decode($inputContent, true);
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string|min:1|max:250',
            'description'=> 'required|string|min:1',
            'icon'=> 'required|image',
            'created_by'=> 'required|string|min:1|max:150',
        ], [
            'name.required' => 'Le champ Nom est obligatoire.',
            'description.required' => 'Le champ description est obligatoire.',
            'icon.required' => 'Le champ Icon est obligatoire et doit etre une image.',
            'icon.image' => 'Le champ Icon est obligatoire et doit etre une image.',
            'created_by.required' => 'Le champ :created_by est obligatoire.',
        ]);

        if ($validator->fails()){

            return response(array('success'=>0, 'faillure' => 1, 'raison' => $validator->errors()->first()), 200);
        }

        $icon = $request->file('icon');

        $pathicon = null;
        if(!($icon === null) and $icon->isValid()){
            $pathicon = Storage::disk('local')->put('icons', $icon);
        }

        $service = new Service(Uuid::generate()->string,$request->get('name'), $request->get('description'), $pathicon, 0, $request->get('created_by'));

        $validite = $service->isInsertable();
        if ($validite[0]){
            $service->save();
            return  response(array('success'=>1, 'faillure' => 0, 'response' => "Service cree avec succes"), 200);
        }else{
            if (!($pathicon === null)){
                Storage::disk('local')->delete($pathicon);
            }
            return response(array('success'=>0, 'faillure' => 1, 'raison' => $validite[1]), 200);
        }
    }


    public function getAllService(Request $request){

        if (!($request->get('active') === null)){
            return response(array('success'=>1, 'faillure' => 0, 'response' => Service::where('active', '=', $request->get('active'))->get()), 200);
        }

        return response(array('success'=>1, 'faillure' => 0, 'response' => Service::all()), 200);
    }

    public function getServiceByBidOrByName(Request $request, $bidOrName){
        $services = Service::where('b_id', '=', $bidOrName)->orWhere('name', '=', $bidOrName)->get();
        return response(array('success'=>1, 'faillure' => 0, 'response' => $services), 200);
    }

    public function updateService(Request $request, $bidOrName){
        $services = Service::where('b_id', '=', $bidOrName)->orWhere('name', '=', $bidOrName)->get();
        if (!(count($services) === 1)){
            return response(array('success'=>0, 'faillure' => 1, 'raison' => 'Service non trouve'), 200);
        }

        $service = $services[0];

        $validator = Validator::make($request->all(), [
            'name'=> 'required|string|min:1|max:250',
            'description'=> 'required|string|min:1',
            'created_by'=> 'required|string|min:1|max:150',
        ], [
            'name.required' => 'Le champ Nom est obligatoire.',
            'description.required' => 'Le champ description est obligatoire.',
            'created_by.required' => 'Le champ :created_by est obligatoire.',
        ]);

        if ($validator->fails()){
            return response(array('success'=>0, 'faillure' => 1, 'raison' => $validator->errors()->first()), 200);
        }

        $icon = $request->file('icon');

        $pathicon = null;
        if(!($icon === null) and $icon->isValid()){

            $pathicon = Storage::disk('local')->put('icons', $icon);
        }

        $selectedServicesByName = Service::where('name', '=', $request->get('name'))->get();
        if (count($selectedServicesByName) > 0 and !($selectedServicesByName[0]->b_id === $service->b_id)){
            return response(array('success'=>0, 'faillure' => 1, 'raison' => 'Erreur: Nom de service deja utilise'), 200);
            //return [false, 'Erreur: Nom de service deja utilise'];
        }

        $service->name = $request->get('name');
        $service->description = $request->get('description');
        $service->created_by = $request->get('created_by');


        $oldpathicon = null;
        if (!($pathicon === null)){
            $oldpathicon = $service->icon;
            $service->icon = $pathicon;
        }

        $b = $service->save();
        if ($b){
            Storage::disk('local')->delete($oldpathicon);
        }


        return response(array('success'=>1, 'faillure' => 0, 'response' => 'Service Ajourne avec succes'), 200);
    }

    public function deleteService(Request $request, $bidOrName){

        $services = Service::where('b_id', '=', $bidOrName)->orWhere('name', '=', $bidOrName)->get();
        if (!(count($services) === 1)){
            return response(array('success'=>0, 'faillure' => 1, 'raison' => 'Service non trouve'), 200);
        }

        $service = $services[0];
        Storage::disk('local')->delete($service->icon);
        $service->delete();
        return response(array('success'=>1, 'faillure' => 0, 'response' => 'Service supprime avec succes'), 200);
    }

    public function getIcon(Request $request, $bidOrName){

        $services = Service::where('b_id', '=', $bidOrName)->orWhere('name', '=', $bidOrName)->get();

        if (!(count($services) === 1)){
            return response()->make("", 404, array(
                'Content-Type' => (new \finfo(FILEINFO_MIME))->buffer("")
            ));
        }
        $exists = Storage::disk('local')->exists($services[0]->icon);
        if (!$exists){
            return response()->make("", 404, array(
                'Content-Type' => (new \finfo(FILEINFO_MIME))->buffer("")
            ));
        }
        $contents = Storage::disk('local')->get($services[0]->icon);
        return response()->make($contents, 200, array(
            'Content-Type' => (new \finfo(FILEINFO_MIME))->buffer($contents)
        ));

    }



    public function activateOrDeactivateService(Request $request, $bidOrName){
        $services = Service::where('b_id', '=', $bidOrName)->orWhere('name', '=', $bidOrName)->get();
        if (!(count($services) === 1)){
            return response(array('success'=>0, 'faillure' => 1, 'raison' => 'Service non trouve'), 200);
        }

        $validator = Validator::make($request->all(), [
            'active'=> 'required|numeric|min:0|max:1',
        ], [
            'active.required' => 'Le champ active est obligatoire.',
            'active.min' => 'Le champ Active doit etre superieur ou egale a 0.',
            'active.max' => 'Le champ active doit etre inferieur ou egale a 1.',
        ]);

        if ($validator->fails()){

            return response(array('success'=>0, 'faillure' => 1, 'raison' => $validator->errors()->first()), 200);
        }

        $service = $services[0];
        $service->active = $request->get('active');
        $service->save();
        return response(array('success'=>1, 'faillure' => 0, 'response' => 'Service Ajourne avec succes'), 200);

    }

}
