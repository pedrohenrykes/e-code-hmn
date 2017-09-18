<?php

class BauComorbidadesRecord extends TRecord
{
    const TABLENAME  = "baucomorbidades";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $cidNome;
    private $cidCodigo;
    private $cidCodNome;

    public function get_cid_nome()
    {
        if ( empty( $this->cidNome ) ) {
            $this->cidNome = new CidRecord( $this->cid_id );
        }

        return $this->cidNome->nomecid;
    }

    public function get_cid_codigo()
    {
        if ( empty( $this->cidCodigo ) ) {
            $this->cidCodigo = new CidRecord( $this->cid_id );
        }

        return $this->cidCodigo->codigocid;
    }

    public function get_cid_codnome()
    {
        if ( empty( $this->cidCodNome ) ) {
            $this->cidCodNome = new VwCidRecord( $this->cid_id );
        }

        return $this->cidCodNome->nomecid;
    }
}
