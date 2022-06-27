<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use log;

class CalculateDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    // private $x;
    // protected $x;
    private $customerId;

    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customerId = $this->customerId;
        // dd($customerId);
        $this->insertDB($customerId);
        sleep(1);
    }

    public function insertDB($x)
    {
        // dd($x->QUOTATION_ID);
        try {

            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                ->where('date', $dateNow)
                ->get();

            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $x->QUOTATION_ID,
                'APP_ID' => $x->APP_ID,
                'TRANSECTION_TYPE' => 'INVOICE',
                'TRANSECTION_ID' => $x->INVOICE_ID,
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'Success',
                'SMS_RESPONSE_JOB_ID' => 'Success',
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => '0812345678',
                'CONTRACT_ID' => $x->CONTRACT_ID,
                'DUE_DATE' => $x->DUE_DATE,
            ]);
        } catch (Exception $e) {

            date_default_timezone_set('Asia/bangkok');
            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');
            $new_error_id = date("Ymdhis");

            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $x[0]->QUOTATION_ID,
                'APP_ID' => $x[0]->APP_ID,
                'TRANSECTION_TYPE' => 'INVOICE',
                'TRANSECTION_ID' => $x[0]->INVOICE_ID,
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => '0812345678',
                'CONTRACT_ID' => $x[0]->CONTRACT_ID,
                'DUE_DATE' => $x[0]->DUE_DATE,
                'SMS_TEXT_MESSAGE' => $e->getMessage(),
            ]);
        }
    }
}
