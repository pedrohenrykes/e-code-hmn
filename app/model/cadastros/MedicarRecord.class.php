<?php

class MedicarRecord extends TRecord
{
    const TABLENAME  = "baumedicar";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $medicamento;
    private $paciente;

    function get_paciente_nome(){
        $atendimento = new BauAtendimentoRecord( $this->bauatendimento_id);
        $paciente = new PacienteRecord( $atendimento->paciente_id);
        //var_dump($paciente);
        //exit();
        if (!empty ($this->paciente)){
            $this->paciente = new PacienteRecord($atendimento->paciente_id);
        }

        return $this->paciente['nomepaciente'];
    }
    

    function get_medicamento_nome(){

        if (empty ($this->medicamento)){
            $this->medicamento = new MedicamentoRecord($this->medicamento_id);
        }
        
        return $this->medicamento->nomemedicamento;

    }

}
