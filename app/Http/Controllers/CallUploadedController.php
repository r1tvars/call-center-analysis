<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CallRecord;

class CallUploadedController extends Controller
{
    public function index(){

       $callRecords = CallRecord::all();

       return view('uploaded', ['records' => $callRecords]);

    }

    public function getFirst(int $id){
        $callRecords = CallRecord::findOrFail($id);

        return view('card', ['card' => $callRecords]);
     }


}
