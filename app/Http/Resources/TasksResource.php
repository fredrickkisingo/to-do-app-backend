<?php
namespace App\Http\Resources;
namespace Vivinet\MovingMedia\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Vivinet\MovingMedia\Http\Resources\Collection\HeadingCollection;

/**
 * Class TasksResource.
 *
 * TasksResource is a JSON resource class that transforms a Episode model into a JSON array with specified fields.
 *
 * 
 */
class TasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
     
        return [
            'id' => (int) $this->id,
            'name'=> $this->name,
            'description'=> $this->description,
            'due_date'=> (string) optional($this->due_date)->diffForHumans()
           
        ];
    }
}
