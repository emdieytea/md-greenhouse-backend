<?php

namespace App\Console\Commands;

use App\Models\Node;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckBatchStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check_batch_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of the batches on the web.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Executing checking of status of the batches on the web!');

        try {
            DB::beginTransaction();
            
            $date_now = Carbon::now();

            $nodes = Node::where('status', 1)->get();

            if ($nodes->isNotEmpty()) {
                foreach ($nodes as $key => $node) {

                    // get the batches from env and convert them to array
                    $batches = explode(',', env('APP_ACTIVE_BATCHES')); // can be commented / removed if not need to rely on env active batch

                    // loop through the batches if it has array value
                    if (array_search($node->batch_no, $batches) === false) { // can be commented / removed if not need to rely on env active batch
                        if ($date_now->diffInMinutes($node->updated_at) > 3) {
                            $node->status = 0;
                            $node->save();

                            $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Batch # ' . $node->batch_no . ' was checked and deemed offline.');
                        } else {
                            $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Batch # ' . $node->batch_no . ' was checked and is still online.');
                        }
                    } // can be commented / removed if not need to rely on env active batch
                }

                DB::commit();

                $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Status of the batches on the web has been checked successfully.');
                return 200;
            }

            $this->error('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Failed to check, please try again!');
            return 400;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Something went wrong please try again!'); // $e->getMessage()
            return 400;
        }
    }
}
