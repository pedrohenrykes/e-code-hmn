<?php

class FarmaciaRecord extends TRecord {

    const TABLENAME = 'farmacia';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';


    private $unidade;

    public function get_nome_unidade()
    {
        if ( empty( $this->medicamento ) ) {
            $this->unidade = new UnidadeDeSaudeRecord( $this->unidadesaude_id );
        }

        return $this->unidade->nomeunidade;
    }

}
