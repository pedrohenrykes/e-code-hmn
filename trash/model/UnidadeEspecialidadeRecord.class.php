<?php

use Adianti\Database\TRecord;

/**
 *
 * @author Danilo Cunha
 */
class UnidadeEspecialidadeRecord extends TRecord
{
    const TABLENAME  = "unidadeespecialidade";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";
    
   	private $especialidade;
   	private $nome_unidade;


   	public function get_especialidade_nome()
    {
    	  if (empty ($this->especialidade)) {
            $this->especialidade = new EspecialidadeRecord($this->especialidade_id);
        }
        return $this->especialidade->nomeespecialidade;
    }

    /*
    public function get_nome_unidade()
    {
    	 if (empty ($this->nome_unidade)) {
            $this->nome_unidade = new UnidadeRecord($this->id);
        }
        return $this->nome_unidade->name;
    }
    */



}
