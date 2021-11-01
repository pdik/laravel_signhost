<?php

namespace pdik\signhost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use pdik\signhost\Response;
use pdik\signhost\dto\consentverification;
use pdik\signhost\dto\digidverification;
use pdik\signhost\dto\eherkenningverification;
use pdik\signhost\dto\eidasloginverification;
use pdik\signhost\dto\filemetadata;
use pdik\signhost\dto\formsetfield;
use pdik\signhost\dto\formsets;
use pdik\signhost\dto\idealverification;
use pdik\signhost\dto\idinverification;
use pdik\signhost\dto\itsmeidentificationverification;
use pdik\signhost\dto\itsmesignverification;
use pdik\signhost\dto\location;
use pdik\signhost\dto\phonenumberverification;
use pdik\signhost\dto\receiver;
use pdik\signhost\dto\scribbleverification;
use pdik\signhost\dto\signer;
use pdik\signhost\dto\signingcertificateverification;
use pdik\signhost\dto\surfnetverification;
use pdik\signhost\dto\transaction;
use pdik\signhost\dto\verification;
class signatures extends Model
{
    protected $table = 'signhost-signatures';

}