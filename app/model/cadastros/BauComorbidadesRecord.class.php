<?php

class BauComorbidadesRecord extends TRecord
{
    const TABLENAME  = "baucomorbidades";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $cidNome;
    private $cidCodigo;

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
}
