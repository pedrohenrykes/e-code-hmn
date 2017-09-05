<?php

class BauUsoMedicacoesRecord extends TRecord
{
    const TABLENAME  = "bauusomedicacoes";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $medicamento;

    public function get_medicamento_nome()
    {
        if ( empty( $this->medicamento ) ) {
            $this->medicamento = new MedicamentoRecord( $this->medicamento_id );
        }

        return $this->medicamento->nomemedicamento;
    }
}
