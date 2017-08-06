<?php

use Adianti\Database\TRecord;

class FontAwesomeIconsModel extends TRecord
{
    const TABLENAME  = "fontawesomeicons";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function __construct( $id = null )
    {
        parent::__construct( $id );
        parent::addAttribute( "class" );
        parent::addAttribute( "unicode" );
    }
}
