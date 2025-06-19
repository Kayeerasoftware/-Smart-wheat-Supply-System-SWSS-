<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
=======
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
