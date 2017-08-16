<?php

use Adianti\Database\TRecord;

class DashBoardModel extends TRecord
{
    const TABLENAME  = "dashboard";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function __construct( $id = null )
    {
        parent::__construct( $id );
        parent::addAttribute( "dataview" );
        parent::addAttribute( "quantifier" );
        parent::addAttribute( "title" );
        parent::addAttribute( "icon" );
        parent::addAttribute( "color" );
        parent::addAttribute( "page" );
        parent::addAttribute( "action" );
    }
}
