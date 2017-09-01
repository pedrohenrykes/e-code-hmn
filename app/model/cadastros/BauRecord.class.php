<?php

class BauRecord extends TRecord
{
    const TABLENAME  = "bau";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    private $paciente;

    public function get_paciente_nome()
    {
        if ( empty( $this->paciente ) ) {
            $this->paciente = new PacienteRecord( $this->paciente_id );
        }

        return $this->paciente->nomepaciente;
    }

    public function getComorbidades()
    {
        $repository = new TRepository( "BauComorbidades" );

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->setProperty( "limit", $limit );
        $criteria->add( new TFilter( "bau_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );
        
        return isset( $objects ) ? $objects : null;
    }

    public function getMedicacoes()
    {
        $repository = new TRepository( "BauUsoMedicacoes" );

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->setProperty( "limit", $limit );
        $criteria->add( new TFilter( "bau_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );
        
        return isset( $objects ) ? $objects : null;
    }

    public function getAlergias()
    {
        $repository = new TRepository( "BauAlergiaMedicamentosa" );

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->setProperty( "limit", $limit );
        $criteria->add( new TFilter( "bau_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );
        
        return isset( $objects ) ? $objects : null;
    }
}
