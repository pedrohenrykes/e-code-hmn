<?php
class DocenteRecord extends TRecord
{
    const TABLENAME = "docente";
    const PRIMARYKEY = "id";
    const IDPOLICY = "serial";

    public function __construct( $id = NULL )
    {
        parent::__construct( $id );

        parent::addAttribute( "chapa" );
        parent::addAttribute( "nomedocente" );
        parent::addAttribute( "email" );
        parent::addAttribute( "contato" );
        
        
    }
}