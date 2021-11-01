<?php

namespace pdik\signhost\Http\Controllers;
use Illuminate\Http\Request;
use Pdik\signhost\Models\signatures;
class Signature extends controller
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
