<?php

namespace Pdik\laravel_signhost\Http\Controllers;
use Illuminate\Http\Request;
use Pdik\laravel_signhost\SignHost;
class Signature extends Controller
{
   public function index(){
      echo "Signhost Controller Show all";
   }
    public function show()
   {
      echo "Signhost Controller Show document";
   }

   public function create(){

   }
}
