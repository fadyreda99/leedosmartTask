<?php

namespace App\Console\Commands;

use App\Models\Post;

use Carbon\Carbon;
use Illuminate\Console\Command;


class DeletedAllSoftDeletesDAily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forceDelete:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all soft deletes posts for more 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $trashedPosts =  Post::onlyTrashed()->get();
        foreach ($trashedPosts as $post){

            $toDate = Carbon::parse(date("Y-m-d"));
            $fromDate = Carbon::parse($post['deleted_at']->format("Y-m-d"));
            $count =  $toDate->diffInDays($fromDate);

            if($count > 30){
                $post->forceDelete();
                info('deleted');
            }else{
                info('nothing deleted');
            }
        }
        return Command::SUCCESS;
    }
}
