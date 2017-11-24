<?php

class BauDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    private $changeFields;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_bau" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id            = new THidden("id");
        $paciente_id   = new THidden("paciente_id");
        $paciente_nome = new TEntry("paciente_nome");
        $dataentrada   = new TDate("dataentrada");
        $horaentrada   = new TDateTime("horaentrada");
        $responsavel   = new TEntry("responsavel");
        $convenio_id   = new TDBCombo("convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");
        $rgresponsavel = new TEntry("rgresponsavel");

        $fk = filter_input( INPUT_GET, "fk" );

        try {

            TTransaction::open( "database" );

            $paciente = new PacienteRecord( $fk );

            if( isset( $paciente ) ) {
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            new TMessage( "error", "Não foi possível carregar os dados do paciente.<br><br>" . $ex->getMessage() );

        }

        $paciente_nome->setSize( "38%" );
        $dataentrada->setSize( "38%" );
        $horaentrada->setSize( "38%" );
        $responsavel->setSize( "38%" );
        $convenio_id->setSize( "38%" );
        $rgresponsavel->setSize( "38%" );

        $convenio_id->setDefaultOption( "..::SELECIONE::.." );

        try {
            TTransaction::open( "database" );
            $repo = new TRepository( "ConvenioRecord" );
            $cri = new TCriteria();
            $cri->add( new TFilter( "nome", "=", "SUS" ) );
            $obj = $repo->load( $cri );
            foreach ( $obj as $ob ) {
                $convenio_id->setValue( $ob->id );
                break;
            }
            TTransaction::close();
        } catch ( Exception $e ) {
            TTransaction::rollback();
            new TMessage( "erro", "Não foi possível carregar o convênio." );
        }

        $rgresponsavel->setMask("9!");

        $horaentrada->setMask( "hh:ii" );
        $dataentrada->setMask( "dd/mm/yyyy" );
        $dataentrada->setDatabaseMask("yyyy-mm-dd");

        $dataentrada->setValue( date( "d/m/Y" ) );
        $horaentrada->setValue( date( "H:i" ) );

        $dataentrada->setEditable( false );
        $horaentrada->setEditable( false );
        $paciente_nome->setEditable( false );

        $responsavel->forceUpperCase();
        $responsavel->setProperty( "title", "Caso o paciente seja menor de idade." );

        $paciente_nome->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $dataentrada->addValidation( TextFormat::set( "Data de Entrada" ), new TRequiredValidator );
        $horaentrada->addValidation( TextFormat::set( "Hora de Entrada" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Nome do Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data de Entrada: {$redstar}" ) ], [ $dataentrada ] );
        $this->form->addFields( [ new TLabel( "Hora de Entrada: {$redstar}" ) ], [ $horaentrada ] );
        $this->form->addFields( [ new TLabel( "Nome do Responsável:" ) ], [ $responsavel ] );
        $this->form->addFields( [ new TLabel( "RG do Responsável:" ) ], [ $rgresponsavel ] );
        $this->form->addFields( [ new TLabel( "Convênio:" ) ], [ $convenio_id ] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );

        $this->form->addAction( "Registrar Boletim", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Listagem de Pacientes", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );

        $this->datagrid = new TDatagridTables();

        $column_paciente_nome = new TDataGridColumn( "paciente_nome", "Nome do Paciente", "left" );
        $column_dataentrada   = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada   = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_responsavel   = new TDataGridColumn( "responsavel", "Nome do Responsável", "left" );
        $column_rgresponsavel = new TDataGridColumn( "rgresponsavel", "RG do Responsável", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );
        $this->datagrid->addColumn( $column_responsavel );
        $this->datagrid->addColumn( $column_rgresponsavel );

        $action_edit = new TDatagridTablesAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar Registro" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_edit );

        $action_del = new TDatagridTablesAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar Registro" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_del );

        $action_avaliacao = new TDatagridTablesAction( [ "ClassificacaoRiscoDetail", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-default" );
        $action_avaliacao->setLabel( "Classificações do Boletim" );
        $action_avaliacao->setImage( "fa:stethoscope green fa-lg" );
        $action_avaliacao->setField( "id" );
        $action_avaliacao->setFk( "id" );
        $action_avaliacao->setDid( "paciente_id" );
        $action_avaliacao->setParameter( "page", __CLASS__ );
        $this->datagrid->addAction( $action_avaliacao );

        $this->datagrid->createModel();

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );

        parent::add( $container );
    }

    public function onSave( $param = NULL )
    {
        try {

            $this->form->validate();

            $object = $this->form->getData( "BauRecord" );

            TTransaction::open( "database" );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "BauDetail", "onReload" ] );
            $action->setParameter( "fk", $param[ "fk" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->onReload( $param );

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param = NULL )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new BauRecord( $param[ "key" ] );

                $dataentrada         = new DateTime( $object->dataentrada );
                $horaentrada         = new DateTime( $object->horaentrada );
                $object->dataentrada         = $dataentrada->format("d/m/Y");
                $object->horaentrada         = $horaentrada->format("H:i");

                $this->onReload( $param );

                $this->form->setData( $object );

                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $param = [
                "key" => $param[ "key" ],
                "fk"  => $param[ "fk" ]
            ];

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameters( $param );
            $action2->setParameters( $param );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    public function Delete( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $object = new BauRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload( $param );

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "desc"
            ];

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->add( new TFilter( "paciente_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $dataentrada = new DateTime( $object->dataentrada );
                    $horaentrada = new DateTime( $object->horaentrada );

                    $object->dataentrada = $dataentrada->format("d/m/Y");
                    $object->horaentrada = $horaentrada->format("H:i");

                    $this->datagrid->addItem( $object );

                }

            }

            $criteria->resetProperties();

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }
}
