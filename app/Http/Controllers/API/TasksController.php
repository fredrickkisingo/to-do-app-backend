<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Task;
use DateTime;
use App\Models\UserTask;
class TasksController extends Controller
{
    use ApiResponser;

    public function index(){
        try {

            $user= auth()->user();
            $user_tasks = UserTask::where('user_id', $user->id)->get();
            $tasks = Task::all();

            $user_tasks = UserTask::join('tasks', 'user_tasks.task_id', '=', 'tasks.id')
                ->where('user_id', $user->id)
                ->get();

            return response()->json($tasks);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        
    }


    public function tasksChart(Request $request){
        
        $users = User::all();
        $tasks = Task::all();
        $data = [];
        foreach ($users as $user) {
        $taskCount = 0;
        foreach ($tasks as $task) {
            $count = UserTask::where('user_id', $user->id)
                            ->where('task_id', $task->id)
                            ->count();
            $taskCount += $count;
        }
        $data[] = [
            'label' => $user->email,
            'value' => $taskCount
        ];
        // return the data as JSON
        return response()->json($data);
}
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status_id' => 'required',
        ]);

        if($validator->fails()) {

            return $this->errorResponse($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

          DB::beginTransaction();
         
        
        $parsed_date=Carbon::parse($request->due_date);
        $date = new DateTime($parsed_date);
        $formattedDate = $date->format('Y-m-d H:i:s');

        try {
            $task = new Task();
            $task->name = $request->name;
            $task->description = $request->description;
            $task->due_date = $formattedDate;
            $task->status_id = $request->status_id;
            $task->save();


            //in my system ongoing will stand for a started project as of time of submission
            //so if status is only ongoing save the start time as now then add a new record to the usertasks model with the task id and user id
            
                $user_task = new UserTask();

                $user_id= auth()->user()->id;
                $user_task->user_id = $user_id;
                $user_task->task_id = $task->id;
                $user_task->start_time = Carbon::now();
                $user_task->status_id = $request->status_id;
                $user_task->due_date = $formattedDate;
                $user_task->save();
            
            DB::commit();


            return $this->successResponse($task);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            DB::rollBack();
        }

    }
        public function show($id){
            try {
                $task = Task::find($id);
                if(!$task) {
                    return $this->notFound();
                }

                return $this->successResponse($task);
            } catch (\Throwable $th) {
                return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        public function update(Request $request,$id){
            try {
                DB::beginTransaction();

                $parsed_date=Carbon::parse($request->due_date);
                $date = new DateTime($parsed_date);
                $formattedDate = $date->format('Y-m-d H:i:s');

                $task = Task::findOrFail($id);
                $task->name = $request->name;
                $task->description = $request->description;
                $task->due_date = $formattedDate;
                $task->status_id = $request->status_id;
                $task->save();

                \Log::info("Tasks are here");
                //in my system ongoing will stand for a started project as of time of submission
                //update if status is either incomplete or completed


                //only execute when its either incomplete or completed
                if($request->status_id  == 4 || $request->status_id == 1){
                    if($request->status_id  == 4 || $request->status_id == 1){
                        $user_task = UserTask::where('task_id', $id)->first();
                        $user_task->status_id = $request->status_id;
                        $user_task->remarks = $request->remark;
    
                    }else if($request->status_id == 4){
                        $user_task = UserTask::where('task_id', $id)->first();
    
                        $user_task->end_time = Carbon::now();
    
                    }
                        $user_task->save();
                }
              
                


              
            } catch (\Throwable $th) {
                return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            DB::commit();
            return $this->successResponse($task);


        }
        public function destroy($id){
            try {
                $task = Task::find($id);
                if(!$task) {
                    return $this->notFound();
                }



                $task->delete();

                return $this->deleteMessage('Task', $id);

            } catch (\Throwable $th) {
                return $this->errorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
                DB::rollBack();

            }
        }
    

}
