<?php

class FarmaciaEntradaItemsRecord extends TRecord {

    const TABLENAME = 'farmaciaentradadetalhe';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';


    private $campo;

    public function get_nome_produto()
    {
        if ( empty( $this->campo ) ) {
            $this->campo = new FarmaciaProdutoRecord( $this->farmaciaproduto_id );
        }

        return $this->campo->nomeproduto;
    }

}
