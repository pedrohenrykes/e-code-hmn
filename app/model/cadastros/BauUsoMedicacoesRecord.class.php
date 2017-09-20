<?php

class BauUsoMedicacoesRecord extends TRecord
{
    const TABLENAME  = "bauusomedicacoes";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $medicamento;
    private $paciente;
    private $principio;

    public function get_medicamento_nome()
    {
        if ( empty( $this->medicamento ) ) {
            $this->medicamento = new MedicamentoRecord( $this->medicamento_id );
        }

        return $this->medicamento->nomemedicamento;
    }

    public function get_paciente_nome()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new PacienteRecord( $this->paciente_id );
        }

        return $this->paciente->nomepaciente;
    }
    public function get_principio_nome()
    {
        if ( empty( $this->principio ) ) {
            $this->principio = new PrincipioAtivoRecord( $this->principioativo_id );
        }

        return $this->principio->nomeprincipioativo;
    }
}
