<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Traits\ApiResponser;

class StatusController extends Controller
{
    use ApiResponser;
    public function index()
    {
        $status = Status::all();
        return  response()->json($status);
    }

    public function store(Request $request){
        $status = Status::create([
            'name' => $request->name
        ]);

      return $this->successResponse($status);
    }

    public function show($id){

        $status = Status::findOrFail($id);
        return $this->successResponse($status);
    }

    public function update(Request $request, $id){

        $status = Status::findOrFail($id);
        if (!$status) {
            return $this->notFound();
        }
        $status->update([
            'name' => $request->input('name')
        ]);
        return $this->successResponse($status);
    }

    public function destroy($id){

        
        $status = Status::findOrFail($id);
        if (!$status) {
            return $this->notFound();
        }

        $status->delete();
        return $this->deleteMessage('Status', $id);
    }

}
