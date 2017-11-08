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

        $id                    = new THidden("id");
        $paciente_id           = new THidden("paciente_id");
        $paciente_nome         = new TEntry("paciente_nome");
        $dataentrada           = new TDate("dataentrada");
        $horaentrada           = new TDateTime("horaentrada");
        $responsavel           = new TEntry("responsavel");
        $convenio_id           = new TDBCombo("convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");
        $queixaprincipal       = new TText("queixaprincipal");

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

        $id                    = new THidden("id");
        $paciente_id           = new THidden("paciente_id");
        $paciente_nome         = new TEntry("paciente_nome");
        $dataentrada           = new TDate("dataentrada");
        $horaentrada           = new TDateTime("horaentrada");
        $responsavel           = new TEntry("responsavel");
        $convenio_id           = new TDBCombo("convenio_id", "database", "ConvenioRecord", "id", "nome", "nome");
        $queixaprincipal       = new TText("queixaprincipal");

        $paciente_nome           ->setSize( "38%" );
        $dataentrada             ->setSize( "38%" );
        $horaentrada             ->setSize( "38%" );
        $responsavel             ->setSize( "38%" );
        $convenio_id             ->setSize( "38%" );
        $queixaprincipal         ->setSize( "38%" );

        $convenio_id             ->setDefaultOption( "..::SELECIONE::.." );

        $horaentrada        ->setMask( "hh:ii" );
        $dataentrada        ->setMask( "dd/mm/yyyy" );
        $dataentrada        ->setDatabaseMask("yyyy-mm-dd");

        $dataentrada->setValue( date( "d/m/Y" ) );
        $horaentrada->setValue( date( "H:i" ) );

        $dataentrada->setEditable( false );
        $horaentrada->setEditable( false );
        $paciente_nome->setEditable( false );

        $responsavel->forceUpperCase();
        $responsavel->setProperty( "title", "Caso o paciente seja menor de idade." );

        $dataentrada->addValidation( TextFormat::set( "Data de Entrada:" ), new TRequiredValidator );

        $page1 = new TLabel( "Identificação", "#7D78B6", 12, "bi");
        $page1->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Identificação" );
        $this->form->addContent( [ $page1 ] );
        $this->form->addFields( [ new TLabel( "Nome do Paciente:") ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data de Entrada:" ) ], [ $dataentrada ] );
        $this->form->addFields( [ new TLabel( "Hora de Entrada:" ) ], [ $horaentrada ] );
        $this->form->addFields( [ new TLabel( "Responsável:") ], [ $responsavel ] );
        $this->form->addFields( [ new TLabel( "Convênio:" ) ], [ $convenio_id ] );
        $this->form->addFields( [ new TLabel( "Queixa Principal:" ) ], [ $queixaprincipal ] );
        $this->form->addFields( [ $id, $paciente_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome   = new TDataGridColumn( "paciente_nome", "Paciente", "left" );
        $column_dataentrada     = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada     = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_queixaprincipal = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );
        $this->datagrid->addColumn( $column_queixaprincipal );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_del );

        $action_avaliacao = new CustomDataGridAction( [ "ClassificacaoRiscoDetail", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-default" );
        $action_avaliacao->setLabel( "Classificações" );
        $action_avaliacao->setImage( "fa:stethoscope green fa-lg" );
        $action_avaliacao->setField( "id" );
        $action_avaliacao->setFk( "id" );
        $action_avaliacao->setDid( "paciente_id" );
        $action_avaliacao->setParameter( "page", __CLASS__ );
        $this->datagrid->addAction( $action_avaliacao );

        $this->datagrid->createModel();

        $onReload = new TAction( [ $this, "onReload" ] );
        $onReload->setParameter( "fk", $fk );

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( $onReload );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

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

            if ( empty( $param[ "order" ] ) ) {
                $param[ "order" ] = "dataentrada";
                $param[ "direction" ] = "desc";
            }

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );
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

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $param );
            $this->pageNavigation->setLimit( $limit );

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }
}
