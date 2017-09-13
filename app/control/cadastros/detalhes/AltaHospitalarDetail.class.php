<?php

class AltaHospitalarDetail extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Alta de Pacientes" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_alta_hospitalar" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                    = new THidden( "id" );
        $paciente_id           = new THidden( "paciente_id" );
        $paciente_nome         = new TEntry("paciente_nome");
        $dataaltahospitalar    = new TDate("dataaltahospitalar");
        $horaaltahospitalar    = new TDateTime("horaaltahospitalar");
        $medicoalta_id         = new TCombo("medicoalta_id");

        $tipoaltahospitalar_id = new TDBCombo(
            "tipoaltahospitalar_id", "database",
            "TipoAltaHospitalarRecord", "id",
            "nometipoaltahospitalar", "nometipoaltahospitalar"
        );

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        try {

            TTransaction::open( "database" );

            $bau      = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( isset( $paciente ) ){
                $id->setValue( $bau->id );
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            new TMessage( "error", "Não foi possível carregar os dados do paciente.<br><br>" . $ex->getMessage() );

        }

        $paciente_nome        ->setSize( "38%" );
        $tipoaltahospitalar_id->setSize( "38%" );
        $dataaltahospitalar   ->setSize( "38%" );
        $horaaltahospitalar   ->setSize( "38%" );
        $medicoalta_id        ->setSize( "38%" );

        $paciente_nome        ->setEditable( false );
        $tipoaltahospitalar_id->setDefaultOption( "..::SELECIONE::.." );
        $medicoalta_id        ->setDefaultOption( "..::SELECIONE::.." );

        $horaaltahospitalar->setMask( "hh:ii" );
        $dataaltahospitalar->setMask( "dd/mm/yyyy" );
        $dataaltahospitalar->setDatabaseMask("yyyy-mm-dd");

        $this->form->addFields( [ new TLabel( "Nome do Paciente:" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Tipo de Alta:" ) ], [ $tipoaltahospitalar_id ] );
        $this->form->addFields( [ new TLabel( "Data da Alta:" ) ], [ $dataaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Hora da Alta:" ) ], [ $horaaltahospitalar ] );
        $this->form->addFields( [ new TLabel( "Médico Responsável:" ) ], [ $medicoalta_id ] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onReload = new TAction( [ "PacientesAltaHospitalarList", "onReload" ] );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave( $param = null )
    {
        try {

            $this->form->validate();

            $object = $this->form->getData( "BauRecord" );

            TTransaction::open( "database" );

            unset($object->paciente_nome);
            $object->situacao = "ALTA";
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "PacientesAltaHospitalarList", "onReload" ] );
            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onReload(){}
}
