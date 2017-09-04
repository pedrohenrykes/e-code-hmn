<?php

class PacienteRecord extends TRecord
{
    const TABLENAME  = "paciente";
    const PRIMARYKEY = "id";
    const IDPOLICY   = "serial";

    public function getComorbidades()
    {
        $repository = new TRepository( "BauComorbidadesRecord" );

        $properties = [
            "order" => "id",
            "direction" => "desc"
        ];

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->add( new TFilter( "paciente_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );

        return isset( $objects ) ? $objects : null;
    }

    public function getMedicacoes()
    {
        $repository = new TRepository( "BauUsoMedicacoesRecord" );

        $properties = [
            "order" => "id",
            "direction" => "desc"
        ];

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->add( new TFilter( "paciente_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );

        return isset( $objects ) ? $objects : null;
    }

    public function getAlergias()
    {
        $repository = new TRepository( "BauAlergiaMedicamentosaRecord" );

        $properties = [
            "order" => "id",
            "direction" => "desc"
        ];

        $criteria = new TCriteria();
        $criteria->setProperties( $properties );
        $criteria->add( new TFilter( "paciente_id", "=", $this->id ) );

        $objects = $repository->load( $criteria, FALSE );

        return isset( $objects ) ? $objects : null;
    }
}
