<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\TagsResource;
use Carbon\Carbon;


class TaskResource extends JsonResource
{

    public function toArray($request)
    {
        // return parent::toArray($request);
        $endDate = Carbon::parse($this->endDate);
        $currentDate = Carbon::now();
        $daysLeft = $endDate->diffInDays($currentDate);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sequence' => $this->sequence,
            'status' => $this->status,
            'require' => $this->require,
            'groupId' => $this->groupId,
            'endDate' => $this->endDate,
            'daysLeft' => $daysLeft, // Number of days left from current date
            'taskListId' => $this -> taskListId,

            // 'tasklist' => $this -> tasklist,
            'tasklist' => TaskListResource::collection($this->tasklist),

            // 'tags' => $this->tags,
            'tags' => TagsResource::collection($this->tags),

        ];
    }
}