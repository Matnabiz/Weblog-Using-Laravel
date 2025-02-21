<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class PostsExport implements FromQuery, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query(){
        return Post::whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->with('user', 'tags', 'likes');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Body',
            'Created At',
            'Updated At',
            'User ID',
            'Tags',
            'Likes Count'
        ];
    }
}

