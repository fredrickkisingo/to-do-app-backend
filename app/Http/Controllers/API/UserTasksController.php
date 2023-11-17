<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserTask;
class UserTasksController extends Controller
{
    use ApiResponser;
    public function index(){
        
        $user_id=Auth::user()->id;
        $user = User::find($user_id);
        $tasks = $user->tasks;
        return $this->successResponse($tasks);
    }


    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'due_date' => 'nullable|date',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'remarks' => 'nullable|string|max:100',
            'status_id' => 'nullable|exists:status,id'
        ]);

        if($validator->fails()) {
            return $this->errorResponse($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $user_task = new UserTask();
            $user_task->user_id = $request->user_id;
            $user_task->task_id = $request->task_id;
            $user_task->due_date = $request->due_date;
            $user_task->start_time = $request->start_time;
            $user_task->end_time = $request->end_time;
            $user_task->remarks = $request->remarks;
            $user_task->status_id = $request->status_id;
            $user_task->save();
            DB::commit();

            return $this->successResponse($user_task);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            DB::rollBack();
        }

    }

    public function show($id){
        try {
            $user_task = UserTask::find($id);
            if(!$user_task) {
                return $this->notFound();
            }

            return $this->successResponse($user_task);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function update(Request $request,$id){
      
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'task_id' => 'exists:tasks,id',
            'due_date' => 'nullable|date',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'remarks' => 'nullable|string|max:100',
            'status_id' => 'nullable|exists:status,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
        $userTask = UserTask::find($id);
        if (!$userTask) {
            return $this->notFound();
            DB::rollBack();

        }

        $userTask->update($request->all());
        

        DB::commit();

        return $this->successResponse($userTask);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            DB::rollBack();
        }

    }

    public function destroy($id){

        $userTask = UserTask::find($id);
        if (!$userTask) {
            return $this->notFound();
        }

        $userTask->delete();

        return $this->deleteMessage('User Task', $id);

    }
}
