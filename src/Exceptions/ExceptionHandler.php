<?php

namespace HoangDo\JsonApi\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Throwable;

class ExceptionHandler extends Handler
{
   protected function convertExceptionToArray(Throwable $e)
   {
       $resp = parent::convertExceptionToArray($e);
       $resp['status'] = 'failed';
       $resp['times'] = now()->toISOString();

       return $resp;
   }
}