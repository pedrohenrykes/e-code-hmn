<?php

class FarmaciaProdutoRecord extends TRecord {

    const TABLENAME = 'farmaciaproduto';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial';


    private $unidade;
    private $medicamento;

    public function get_nome_unidade()
    {
        if ( empty( $this->nomeunidade ) ) {
            $this->unidade = new UnidadeDeSaudeRecord( $this->unidadesaude_id );
        }

        return $this->unidade->nomeunidade;
    }


    public function get_nome_medicamento()
    {
        if ( empty( $this->medicamento ) ) {
            $this->medicamento = new MedicamentoRecord( $this->medicamento_id );
        }

        return $this->medicamento->nomemedicamento;
    }

}
