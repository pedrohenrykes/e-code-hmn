<?php

class BauComorbidadesRecord extends TRecord
{
    const TABLENAME  = "baucomorbidades";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $cidname;
    private $cidcode;

    public function get_cid_nome()
    {
        if ( empty( $this->cidname ) ) {
            $this->cidname = new CidRecord( $this->cid_id );
        }

        return $this->cidname->nomecid;
    }

    public function get_cid_codigo()
    {
        if ( empty( $this->cidcode ) ) {
            $this->cidcode = new CidRecord( $this->cid_id );
        }

        return $this->cidcode->codigocid;
    }
}
