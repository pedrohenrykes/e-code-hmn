<?php

class FarmaciaMovimentacaoRecord extends TRecord {

    const TABLENAME = 'farmaciamovimentacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';


    private $farmaciaorigem;
    private $farmaciadestino;

    public function get_farmacia_origem()
    {
        if ( empty( $this->farmaciaorigem ) ) {
            $this->farmaciaorigem = new FarmaciaRecord( $this->farmaciaorigem_id );
        }

        return $this->farmaciaorigem->nomefarmacia;
    }

    public function get_farmacia_destino()
    {
        if ( empty( $this->farmaciadestino ) ) {
            $this->farmaciadestino = new FarmaciaRecord( $this->farmaciadestino_id );
        }

        return $this->farmaciadestino->nomefarmacia;
    }

}
