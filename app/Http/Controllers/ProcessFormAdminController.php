<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class ProcessFormAdminController extends Controller
{
  
    private function getvalid($data=[]){
        $valid=[];
        $res=[];
        $valid= $data;
        unset($valid['id']);
        foreach($valid as $key => $value) {   
                
            if(($value['required'] === true) && ($value['maxlength'] > 0)){
                $res[$key]=  'required'.'|max:'.$value['maxlength'];
            }
            
            if(($value['required'] === true) && ($value['maxlength'] === 0)){
                $res[$key]=  'required';
            }
            
            if(($value['required'] === false) && ($value['maxlength'] > 0)){
                //$res[$key]= 'max:'.$value['maxlength'];
            }
        }
        
        return $res;
    }

    public function store(Request $request)
    {       

    try{
        $data= [];
        $form=[];
        $valid=[];
        $data= $request->all();
        $form= json_decode($data[0], true);
        $valid= json_decode($data[1], true);
        $tableName= $request->segment(4);

        $res=[];
        $res= $this->getvalid($valid);       

        // Validaciones del formulario
        $validator = Validator::make($form, $res);

        // Si las validaciones fallan, se redirige al formulario con los errores
        if ($validator->fails()) {
            $request->session()->flash('oldData', $data[0]);
            $request->session()->flash('errors', $validator->errors()->toArray());

            // Redirect back to the form with the old input
            return redirect()->route('create_'.$tableName)->withInput();
        }

        unset($form['_method']);
        unset($form['_token']);
        // Si las validaciones pasan, se crean los registros en la base de datos
        $id = DB::table($tableName)->insertGetId($form);
        //dd($res, $form, $id);
        
        // Se redirige a una página de confirmación
        if(($id > 0) && (!empty($id))){
            return redirect()->route('create_'.$tableName)->json('success', 'created successful!');
        }
    }
    catch(\Exception $e){
       dd($e);
    }
    
    }

    public function update(Request $request, $id) {
        
        $data= [];
        $form=[];
        $valid=[];
        $data= $request->all();
        $tableName= $request->segment(4);

        $res=[];
        $res= $this->getvalid($valid);
        //dd($data, $tableName, $id, $res);
        // Validaciones del formulario
        $validator = Validator::make($data, $res);
            
            if ($validator->fails()) {
                $request->session()->flash('oldData', $data[0]);
                $request->session()->flash('errors', $validator->errors()->toArray());
    
                // Redirect back to the form with the old input
                return redirect()->back()->withInput();
            }

            unset($form['_method']);
            unset($form['_token']);
            $status= DB::table($tableName)->where('id', $request->id)->update($data);
            $messageStatus= ($status > 0)? "Updated $request->id successful! ": 'No Updated';
           // response()->json(['success'=> $request->id.': '.$messageStatus]);
            return redirect()->route('update_'.$tableName, $request->id)->with('success', $messageStatus);
             
        
    }

    public function destroy(Request $request)
    {
        try{
               
            if($request->submit === 'delete'){
                $tableName = $request->segment(2);
                $id = $request->id;
                $deleted = DB::table($tableName)->where('id', $id)->delete();

                if($deleted > 0){
                    return response()->json(['type' => 'success']);
                } else {
                    return response()->json(['type' => 'error']);
                }
            }
        }
        catch(\Exception $e){
            dd($e);
        }
    }
}

