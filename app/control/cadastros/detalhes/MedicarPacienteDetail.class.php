<?php

class MedicarPacienteDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_list_medicar" );
        $this->form->setFormTitle( "Registro de Medicação" );
        $this->form->class = "tform";

        $id                     = new THidden( "id" );
        $paciente_id            = new THidden( "paciente_id" );
        $bau_id                 = new THidden( "bau_id" );
        $profissional_id        = new THidden( "profissional_id" ); 
        $prescricao_id          = new THidden( "prescricao_id" );
        $dataaplicacao          = new TDate( "dataaplicacao" );
        //$exameclinico           = new TText( "exameclinico" );
        //$examescomplementares   = new TText( "examescomplementares" );
        //$diagnosticomedico      = new TText( "diagnosticomedico" );
        $descricaotratamento    = new TText( "descricaotratamento" );

        //$paciente_nome->setSize("60%");
        //$exameclinico->setSize("90%");
        //$examescomplementares->setSize("90%");
        //$descricaotratamento->setSize("90%");
        $dataaplicacao->setSize("45%");

        $prescricao_id = filter_input( INPUT_GET, "key" );
        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);

        try {
            TTransaction::open( "database" );
            $paciente = new PacienteRecord($did);
            if( isset($paciente) ) {
                $paciente_nome->setValue($paciente->nomepaciente);
            }
            TTransaction::close();
        } catch (Exception $ex) {
            $action = new TAction(["PacientesEncaminhamentoList", "onReload"]);
            new TMessage("error", "Ocorreu um erro ao carregar as dependência do formulário.", $action);
        }


        $dataaplicacao->setMask( "dd/mm/yyyy h:i:s" );
        $dataaplicacao->setDatabaseMask("yyyy-mm-dd h:i:s");

        $dataaplicacao->setValue( date( "d/m/Y h:i:s" ) );
        $dataaplicacao->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $dataaplicacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data: {$redstar}" ) ], [ $dataaplicacao ] );
        //$this->form->addFields( [ new TLabel( "Observação:" ) ], [ $exameclinico ] );
        $this->form->addFields( [ $id, $bau_id, $paciente_id, $profissional_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );

        $onReload = new TAction( [ "PacienteMedicacaoList", "onReload" ] );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Confirmar Aplicação", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "medicamento_nome", "Medicação", "left" );
        $column_2 = new TDataGridColumn( "dosagem", "Dose", "left" );
        $column_3 = new TDataGridColumn( "posologia", "Posologia", "left" );
        $column_4 = new TDataGridColumn( "dataprescricao", "Data da Prescrição", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $action_edit->setParameter( "did", $did );
        $this->datagrid->addAction( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $action_del->setParameter( "did", $did );
        $this->datagrid->addAction( $action_del );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();

        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );

    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauAtendimentoRecord" );

        try {
            $object->enfermeiro_id = TSession::getValue('profissionalid');
            $object->status = 'MEDICADO';
            unset( $object->paciente_nome );

            $this->form->validate();
            TTransaction::open( "database" );

            $object->store();
            TTransaction::close();

            $action = new TAction( [ "PacienteMedicacaoList", "onReload" ] );
            $action->setParameters( $param );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            $this->form->setData( $object );
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param = null )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {
                TTransaction::open( "database" );

                $object = new BauUsoMedicacoesRecord( $param[ "key" ] );
                $object->dataprescricao = TDate::date2br($object->dataprescricao) . ' ' . substr($object->dataprescricao, 11, strlen($object->dataprescricao));

                $this->onReload( $param );
                $this->form->setData( $object );
                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }

    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {

            $param = [
            "key" => $param[ "key" ],
            "fk"  => $param[ "fk" ],
            "did"  => $param[ "did" ]
            ];

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameters( $param );
            $action2->setParameters( $param );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    public function Delete( $param = null )
    {
        try {

            TTransaction::open( "database" );
            $object = new BauUsoMedicacoesRecord( $param[ "key" ] );
            $object->delete();
            TTransaction::close();

            $this->onReload( $param );

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReload( $param )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauPrescricaoRecord" );

            $properties = [
            "order" => "dataprescricao",
            "direction" => "desc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );
            $criteria->add( new TFilter( "status", "=", 'PRESCRITO' ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $object->dataprescricao = TDate::date2br($object->dataprescricao) . ' ' . substr($object->dataprescricao, 11, strlen($object->dataprescricao));

                    $this->datagrid->addItem( $object );

                }

            }

            $criteria->resetProperties();

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onClear()
    {
        $this->form->clear();
    }

    
}
