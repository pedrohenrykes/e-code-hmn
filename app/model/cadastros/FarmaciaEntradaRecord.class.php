<?php

class FarmaciaEntradaRecord extends TRecord {

    const TABLENAME = 'farmaciaentrada';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';


    private $farmacia;

    public function get_nome_farmacia()
    {
        if ( empty( $this->farmacia ) ) {
            $this->farmacia = new FarmaciaRecord( $this->farmacia_id );
        }

        return $this->farmacia->nomefarmacia;
    }

}
