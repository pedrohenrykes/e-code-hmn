<?php

use Adianti\Database\TRecord;

class AdminLteColorsModel extends TRecord
{
    const TABLENAME  = "adminltecolors";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function __construct( $id = null )
    {
        parent::__construct( $id );
        parent::addAttribute( "class" );
        parent::addAttribute( "colorname" );
    }
}
