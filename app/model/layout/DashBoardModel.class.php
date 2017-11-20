<?php

use Adianti\Database\TRecord;

class DashBoardModel extends TRecord
{
    const TABLENAME  = "dashboard";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";
}
