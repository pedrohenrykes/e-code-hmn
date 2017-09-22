<?php

class ExameRecord extends TRecord{

    const TABLENAME  = "exame";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $tipoexame;

    public function get_tipo_exame_nome(){

        if ( empty( $this->tipoexame ) ) {
            $this->tipoexame = new TipoExameRecord( $this->tipoexame_id );
        }
        return $this->tipoexame->nome;
    }

}

