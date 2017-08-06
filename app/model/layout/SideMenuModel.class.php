<?php

use Adianti\Database\TRecord;

class SideMenuModel extends TRecord
{
    const TABLENAME  = "sidemenu";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function __construct( $id = null )
    {
        parent::__construct( $id );
        parent::addAttribute( "menu_type" );
        parent::addAttribute( "name" );
        parent::addAttribute( "icon" );
        parent::addAttribute( "sequence" );
        parent::addAttribute( "action_class" );
        parent::addAttribute( "menu_id" );
        parent::addAttribute( "active" );
    }
}