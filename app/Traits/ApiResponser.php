<?php
namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser {
    
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json(['data' => $data], $code);
    }

    public function errorResponse($message, $code)
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }

    public function notFound($code = Response::HTTP_NOT_FOUND)
    {
        return response()->json(['message' => 'model not found', 'code' => $code], $code);
    }

    public function deleteMessage($entity, $id, $code= Response::HTTP_OK)
    {
        return response()->json($entity.' with id '.$id.' deleted successfully', $code);
    }

    public function forbidden($code=Response::HTTP_FORBIDDEN)
    {
        return response()->json(['message' => 'forbidden'], $code);
    }
}
