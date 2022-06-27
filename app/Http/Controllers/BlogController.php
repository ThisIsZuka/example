<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;
use Illuminate\Support\Facades\Redis;
use App\Jobs\CalculateDataJob;
use Exception;

class BlogController extends Controller
{
    public function index()
    {
        try {
          
            $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '01-01-2022' "));

            for ($x = 1; $x <= 100; $x++) {
              // var_dump($x);
            //   if($x % 2 == 0){
            //     CalculateDataJob::dispatch($list_sendSMS[$x]);
            //   }else{
            //     CalculateDataJob::dispatch($list_sendSMS[$x]);
            //   }
              CalculateDataJob::dispatch($list_sendSMS[$x]);
            }

            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        // try {
        //   // create redis instancep
        //   $redis = new \Redis();

        //   // connect with server and port
        //   $redis->connect('localhost', 6379);

        //   // set key
        //   $redis->set('user', 'John Doe');

        //   // get value
        //   $user = $redis->get('user');

        //   print($user); // John Doe
        // } catch (Exception $ex) {
        //   echo $ex->getMessage();
        // }
    }
}
