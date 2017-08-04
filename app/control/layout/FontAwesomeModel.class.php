<?php

use Adianti\Database\TRecord;

class FontAwesomeModel extends TRecord
{
    const TABLENAME  = "fontawesome";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function __construct( $id = null )
    {
        parent::__construct( $id );
        parent::addAttribute( "class" );
        parent::addAttribute( "unicode" );
    }
}